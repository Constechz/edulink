<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    /**
     * Display the billing & subscriptions dashboard.
     */
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $school = School::with('plan')->findOrFail($schoolId);

        $plans = Plan::where('is_active', true)->get();
        $history = Subscription::where('school_id', $schoolId)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();

        $settings = $school->settings ?: [];
        $websiteUnlocked = isset($settings['website_builder_unlocked']) && $settings['website_builder_unlocked'] == true;
        $websiteUnlockPrice = SystemSetting::getVal('website_builder_unlock_price', '500.00');

        $reportCardPrice = SystemSetting::getVal('report_card_price', '0.20');
        $portalUnlockPrice = SystemSetting::getVal('portal_unlock_price', '200.00');
        $portalsUnlocked = isset($settings['portals_unlocked']) && $settings['portals_unlocked'] == true;
        $reportCredits = isset($settings['report_credits']) ? intval($settings['report_credits']) : 0;

        return view('school.billing', compact(
            'school', 'plans', 'history', 'websiteUnlocked', 'websiteUnlockPrice',
            'reportCardPrice', 'portalUnlockPrice', 'portalsUnlocked', 'reportCredits'
        ));
    }

    /**
     * Redirect to simulated Paystack/Flutterwave subscription checkout gateway.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'type' => 'required|in:subscription,website_unlock,portals_unlock,report_credits_purchase',
            'plan_id' => 'required_if:type,subscription|exists:plans,id',
            'cycle' => 'required_if:type,subscription|in:monthly,yearly',
            'gateway' => 'required|in:paystack,flutterwave',
            'credits' => 'required_if:type,report_credits_purchase|integer|min:1',
        ]);

        return redirect()->route('school.billing.gateway', [
            'type' => $request->type,
            'plan_id' => $request->plan_id,
            'cycle' => $request->cycle,
            'gateway' => $request->gateway,
            'credits' => $request->credits,
        ]);
    }

    /**
     * Redirect to simulated Paystack/Flutterwave website builder unlock checkout gateway.
     */
    public function unlockWebsite(Request $request)
    {
        $request->validate([
            'gateway' => 'required|in:paystack,flutterwave',
        ]);

        return redirect()->route('school.billing.gateway', [
            'type' => 'website_unlock',
            'gateway' => $request->gateway,
        ]);
    }

    /**
     * Display the simulated payment checkout gateway page.
     */
    public function gatewayPayment(Request $request)
    {
        $type = $request->get('type', 'subscription');
        $gateway = $request->get('gateway', 'paystack');
        
        $plan = null;
        $amount = 0.00;
        $cycle = $request->get('cycle', 'monthly');

        if ($type === 'subscription') {
            $plan = Plan::findOrFail($request->get('plan_id'));
            $amount = $cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
        } elseif ($type === 'website_unlock') {
            $amount = SystemSetting::getVal('website_builder_unlock_price', '500.00');
        } elseif ($type === 'portals_unlock') {
            $amount = SystemSetting::getVal('portal_unlock_price', '200.00');
        } elseif ($type === 'report_credits_purchase') {
            $credits = intval($request->get('credits', 50));
            $pricePerCredit = floatval(SystemSetting::getVal('report_card_price', '0.20'));
            $amount = $credits * $pricePerCredit;
        }

        $school = School::findOrFail($request->user()->school_id);

        $paystackPublicKey = SystemSetting::getVal('platform_paystack_public_key', '');
        $paystackEnabled = SystemSetting::getVal('platform_paystack_enabled', '0') == '1';

        $classId = $request->get('class_id');
        $termId = $request->get('term_id');
        $academicYearId = $request->get('academic_year_id');

        return view('school.billing.checkout_gateway', compact(
            'type', 'gateway', 'plan', 'amount', 'cycle', 'school',
            'paystackPublicKey', 'paystackEnabled', 'classId', 'termId', 'academicYearId'
        ));
    }

    /**
     * Handle the successful mock gateway checkout callback.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'type' => 'required|in:subscription,website_unlock,portals_unlock,report_credits_purchase',
            'gateway' => 'required|in:paystack,flutterwave',
            'status' => 'required|in:success,failed',
            'plan_id' => 'nullable|exists:plans,id',
            'cycle' => 'nullable|in:monthly,yearly',
            'reference' => 'nullable|string',
            'credits' => 'nullable|integer',
        ]);

        if ($request->status !== 'success') {
            return redirect()->route('school.billing.index')
                ->with('error', 'Payment transaction was canceled or failed at the payment gateway.');
        }

        $schoolId = $request->user()->school_id;
        $school = School::findOrFail($schoolId);
        $reference = $request->input('reference');

        // Verify the payment securely with Paystack API if we are using Paystack and a reference is provided
        if ($request->gateway === 'paystack' && $reference) {
            $secretKey = SystemSetting::getVal('platform_paystack_secret_key');
            if (!$secretKey) {
                return redirect()->route('school.billing.index')
                    ->with('error', 'Paystack integration is not fully configured (Secret Key missing).');
            }

            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $secretKey,
                    'Accept' => 'application/json',
                ])->timeout(15)->get("https://api.paystack.co/transaction/verify/" . urlencode($reference));

                if (!$response->successful()) {
                    return redirect()->route('school.billing.index')
                        ->with('error', 'Failed to communicate with Paystack API for verification.');
                }

                $paymentData = $response->json();

                if (!isset($paymentData['status']) || !$paymentData['status'] || !isset($paymentData['data']['status']) || $paymentData['data']['status'] !== 'success') {
                    return redirect()->route('school.billing.index')
                        ->with('error', 'Payment verification failed: ' . ($paymentData['message'] ?? 'Transaction not found or successful.'));
                }

                // Verify transaction amount matches (Paystack amount is in subunits: e.g. GHS * 100)
                $amountPaid = $paymentData['data']['amount'] / 100;
                $expectedAmount = 0.00;

                if ($request->type === 'subscription') {
                    $plan = Plan::findOrFail($request->plan_id);
                    $expectedAmount = $request->cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
                } elseif ($request->type === 'website_unlock') {
                    $expectedAmount = floatval(SystemSetting::getVal('website_builder_unlock_price', '500.00'));
                } elseif ($request->type === 'portals_unlock') {
                    $expectedAmount = floatval(SystemSetting::getVal('portal_unlock_price', '200.00'));
                } elseif ($request->type === 'report_credits_purchase') {
                    $credits = intval($request->get('credits', 0));
                    $pricePerCredit = floatval(SystemSetting::getVal('report_card_price', '0.20'));
                    $expectedAmount = $credits * $pricePerCredit;
                }

                // Float check with buffer
                if (abs($amountPaid - $expectedAmount) > 0.05) {
                    return redirect()->route('school.billing.index')
                        ->with('error', "Payment verification failed: Amount paid (GHS {$amountPaid}) does not match expected amount (GHS {$expectedAmount}).");
                }

                // Double check currency
                if (strtoupper($paymentData['data']['currency']) !== 'GHS') {
                    return redirect()->route('school.billing.index')
                        ->with('error', "Payment verification failed: Currency mismatch (Expected GHS, got {$paymentData['data']['currency']}).");
                }

                // Reference verified successfully
                $reference = $paymentData['data']['reference'];

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Paystack Verification Exception: ' . $e->getMessage());
                return redirect()->route('school.billing.index')
                    ->with('error', 'An error occurred while verifying the payment: ' . $e->getMessage());
            }
        } else {
            // Generate mock reference if not verified
            if (!$reference) {
                $reference = 'pay_' . $request->gateway . '_' . Str::random(16);
            }
        }

        if ($request->type === 'subscription') {
            $plan = Plan::findOrFail($request->plan_id);
            $amount = $request->cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
            $endsAt = $request->cycle === 'yearly' ? now()->addYear() : now()->addMonth();

            Subscription::create([
                'school_id' => $schoolId,
                'plan_id' => $plan->id,
                'description' => "Plan Subscription: {$plan->name}",
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'amount_paid' => $amount,
                'currency' => 'GHS',
                'payment_reference' => $reference,
                'payment_method' => $request->gateway,
                'status' => 'active',
                'auto_renew' => true,
            ]);

            // Update School plan attributes
            $school->update([
                'plan_id' => $plan->id,
                'subscription_status' => 'active',
                'trial_ends_at' => null, // end trial
            ]);

            return redirect()->route('school.billing.index')
                ->with('success', "Payment of GHS " . number_format($amount, 2) . " processed successfully via " . ucfirst($request->gateway) . ". Upgraded to the {$plan->name} package.");
        } elseif ($request->type === 'website_unlock') {
            $price = SystemSetting::getVal('website_builder_unlock_price', '500.00');
            $settings = $school->settings ?: [];
            $settings['website_builder_unlocked'] = true;
            $school->settings = $settings;
            $school->save();

            Subscription::create([
                'school_id' => $schoolId,
                'plan_id' => null,
                'description' => 'Custom Website Builder Unlock',
                'starts_at' => now(),
                'ends_at' => now(),
                'amount_paid' => $price,
                'currency' => 'GHS',
                'payment_reference' => $reference,
                'payment_method' => $request->gateway,
                'status' => 'active',
                'auto_renew' => false,
            ]);

            return redirect()->route('school.billing.index')
                ->with('success', "Payment of GHS " . number_format($price, 2) . " processed successfully via " . ucfirst($request->gateway) . ". The Custom Website Builder is now permanently unlocked.");
        } elseif ($request->type === 'portals_unlock') {
            $price = SystemSetting::getVal('portal_unlock_price', '200.00');
            $settings = $school->settings ?: [];
            $settings['portals_unlocked'] = true;
            $school->settings = $settings;
            $school->save();

            Subscription::create([
                'school_id' => $schoolId,
                'plan_id' => null,
                'description' => 'Student & Parent Portal Unlock',
                'starts_at' => now(),
                'ends_at' => now(),
                'amount_paid' => $price,
                'currency' => 'GHS',
                'payment_reference' => $reference,
                'payment_method' => $request->gateway,
                'status' => 'active',
                'auto_renew' => false,
            ]);

            return redirect()->route('school.billing.index')
                ->with('success', "Payment of GHS " . number_format($price, 2) . " processed successfully via " . ucfirst($request->gateway) . ". Student & Parent portal access has been successfully activated!");
        } elseif ($request->type === 'report_credits_purchase') {
            $credits = intval($request->get('credits', 0));
            $pricePerCredit = floatval(SystemSetting::getVal('report_card_price', '0.20'));
            $price = $credits * $pricePerCredit;

            $settings = $school->settings ?: [];
            $currentCredits = isset($settings['report_credits']) ? intval($settings['report_credits']) : 0;
            $settings['report_credits'] = $currentCredits + $credits;
            $school->settings = $settings;
            $school->save();

            Subscription::create([
                'school_id' => $schoolId,
                'plan_id' => null,
                'description' => "Purchased {$credits} Report Card Print Credits",
                'starts_at' => now(),
                'ends_at' => now(),
                'amount_paid' => $price,
                'currency' => 'GHS',
                'payment_reference' => $reference,
                'payment_method' => $request->gateway,
                'status' => 'active',
                'auto_renew' => false,
            ]);

            if ($request->filled('class_id') && $request->filled('term_id') && $request->filled('academic_year_id')) {
                return redirect()->route('school.reports.index', [
                    'class_id' => $request->class_id,
                    'term_id' => $request->term_id,
                    'academic_year_id' => $request->academic_year_id,
                ])->with('success', "Payment of GHS " . number_format($price, 2) . " processed successfully. Purchased {$credits} report card print credits! You can now print the report cards immediately.");
            }

            return redirect()->route('school.billing.index')
                ->with('success', "Payment of GHS " . number_format($price, 2) . " processed successfully via " . ucfirst($request->gateway) . ". Purchased {$credits} report card print credits! Your balance is now " . ($currentCredits + $credits) . ".");
        }
    }
}
