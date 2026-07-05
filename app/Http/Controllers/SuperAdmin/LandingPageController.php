<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Show the landing page editor.
     */
    public function edit()
    {
        $settingsKeys = [
            'welcome_hero_badge' => 'ERP Automation Solution',
            'welcome_hero_title' => 'The Intelligent Cloud ERP for Modern Institutions',
            'welcome_hero_sub' => 'Empower your school with a unified platform for academics, real-time fee tracking, automated terminal report cards, and seamless multi-portal communication. Built for institutions striving for excellence.',
            
            'welcome_stat1_value' => '10k+',
            'welcome_stat1_label' => 'Students Enrolled',
            'welcome_stat2_value' => '99.9%',
            'welcome_stat2_label' => 'Uptime SLA',
            'welcome_stat3_value' => '15+',
            'welcome_stat3_label' => 'Smart Modules',
            
            'welcome_pillar1_icon' => 'bi-wallet2',
            'welcome_pillar1_title' => 'Fee & Billing Hub',
            'welcome_pillar1_desc' => 'Automate student invoices, record payments dynamically via mobile money, track partial payment history, and generate digital financial reports.',
            
            'welcome_pillar2_icon' => 'bi-journal-check',
            'welcome_pillar2_title' => 'Academic Reports',
            'welcome_pillar2_desc' => 'Compile terminal grades, calculate GPA averages automatically, customize teacher remarks, and generate beautiful, print-ready student report cards.',
            
            'welcome_pillar3_icon' => 'bi-people-fill',
            'welcome_pillar3_title' => 'Multi-Role Portals',
            'welcome_pillar3_desc' => 'Dedicated dashboards tailored for administrators, teachers, parents, and students. Improve engagement with real-time access to assignments and performance.',
            
            'welcome_pillar4_icon' => 'bi-calendar-event',
            'welcome_pillar4_title' => 'Timetable Planner',
            'welcome_pillar4_desc' => 'Generate clash-free timetables for classes, schedule subject allocations, assign teacher rooms, and organize academic calendars with ease.',
            
            'welcome_price1_title' => 'Starter Trial',
            'welcome_price1_sub' => 'Evaluate basic capabilities',
            'welcome_price1_price' => 'GHS 0',
            'welcome_price1_desc' => 'Great to test the software features with real data before choosing a subscription plan.',
            'welcome_price1_features' => "Max 50 students\nBasic Student Register\nDaily Attendance logs\nSelf-managed onboarding",
            
            'welcome_price2_title' => 'Standard School',
            'welcome_price2_sub' => 'For single campus primary/secondary',
            'welcome_price2_price' => 'GHS 450',
            'welcome_price2_desc' => 'Unlock automated grading and billing. Most chosen by growing private and model institutions.',
            'welcome_price2_features' => "Up to 800 students\nSmart Accounting & Bills\nGrading System & Report Cards\nParent & Teacher Portals\nSMS Notifications support",
            
            'welcome_price3_title' => 'Institution Enterprise',
            'welcome_price3_sub' => 'Custom deployments',
            'welcome_price3_price' => 'Custom',
            'welcome_price3_desc' => 'For school groups with multiple branches, heavy resource operations, or dedicated servers.',
            'welcome_price3_features' => "Unlimited Students\nCustom Branding & Subdomain\nDedicated DB Instance\nPremium 24/7 SLA Support\nAPI Access & Integrations",
            
            'welcome_faq1_q' => 'How long does it take to onboard our school?',
            'welcome_faq1_a' => 'You can register online instantly! Setup takes less than 10 minutes. Once registered, our setup assistant will guide you through adding classes, academic terms, assigning subjects to teachers, and uploading students.',
            
            'welcome_faq2_q' => 'Are parent and student portal accounts free?',
            'welcome_faq2_a' => 'Yes! Once a school subscribes to our platform, there are no extra charges for parents, students, or teacher accounts. All user portals are included in the flat monthly tenant package.',
            
            'welcome_faq3_q' => 'What payment methods are integrated for fees?',
            'welcome_faq3_a' => config('app.name', 'EduLink') . ' integrates natively with major mobile money providers in Ghana (MTN MoMo, Telecel Cash, AT Money) and credit/debit card processors. Parents can pay bills directly online, updating school accounts in real time.',
            
            'welcome_faq4_q' => 'Is our institution\'s data safe and secure?',
            'welcome_faq4_a' => 'Absolutely. We run on enterprise cloud services, utilizing daily automated database backups, multi-factor authentication (MFA) for user accounts, and end-to-end HTTPS encryption to ensure compliance and data safety.',
            
            'welcome_footer_desc' => 'Providing premium SaaS management systems for modern schools across Ghana and the West African sub-region.',
            'welcome_support_email' => 'support@' . strtolower(config('app.name', 'edulink')) . '.gh',
            'welcome_whatsapp_number' => ''
        ];

        $settings = [];
        foreach ($settingsKeys as $key => $default) {
            $settings[$key] = SystemSetting::getVal($key, $default);
        }

        return view('super-admin.landing-page.edit', compact('settings'));
    }

    /**
     * Update the landing page settings.
     */
    public function update(Request $request)
    {
        $rules = [
            'welcome_hero_badge' => 'required|string|max:255',
            'welcome_hero_title' => 'required|string|max:255',
            'welcome_hero_sub' => 'required|string',
            
            'welcome_stat1_value' => 'required|string|max:20',
            'welcome_stat1_label' => 'required|string|max:100',
            'welcome_stat2_value' => 'required|string|max:20',
            'welcome_stat2_label' => 'required|string|max:100',
            'welcome_stat3_value' => 'required|string|max:20',
            'welcome_stat3_label' => 'required|string|max:100',
            
            'welcome_pillar1_icon' => 'required|string|max:100',
            'welcome_pillar1_title' => 'required|string|max:255',
            'welcome_pillar1_desc' => 'required|string',
            
            'welcome_pillar2_icon' => 'required|string|max:100',
            'welcome_pillar2_title' => 'required|string|max:255',
            'welcome_pillar2_desc' => 'required|string',
            
            'welcome_pillar3_icon' => 'required|string|max:100',
            'welcome_pillar3_title' => 'required|string|max:255',
            'welcome_pillar3_desc' => 'required|string',
            
            'welcome_pillar4_icon' => 'required|string|max:100',
            'welcome_pillar4_title' => 'required|string|max:255',
            'welcome_pillar4_desc' => 'required|string',
            
            'welcome_price1_title' => 'required|string|max:255',
            'welcome_price1_sub' => 'required|string|max:255',
            'welcome_price1_price' => 'required|string|max:100',
            'welcome_price1_desc' => 'required|string',
            'welcome_price1_features' => 'required|string',
            
            'welcome_price2_title' => 'required|string|max:255',
            'welcome_price2_sub' => 'required|string|max:255',
            'welcome_price2_price' => 'required|string|max:100',
            'welcome_price2_desc' => 'required|string',
            'welcome_price2_features' => 'required|string',
            
            'welcome_price3_title' => 'required|string|max:255',
            'welcome_price3_sub' => 'required|string|max:255',
            'welcome_price3_price' => 'required|string|max:100',
            'welcome_price3_desc' => 'required|string',
            'welcome_price3_features' => 'required|string',
            
            'welcome_faq1_q' => 'required|string|max:255',
            'welcome_faq1_a' => 'required|string',
            'welcome_faq2_q' => 'required|string|max:255',
            'welcome_faq2_a' => 'required|string',
            'welcome_faq3_q' => 'required|string|max:255',
            'welcome_faq3_a' => 'required|string',
            'welcome_faq4_q' => 'required|string|max:255',
            'welcome_faq4_a' => 'required|string',
            
            'welcome_footer_desc' => 'required|string',
            'welcome_support_email' => 'required|email|max:255',
            'welcome_whatsapp_number' => 'nullable|string|max:20'
        ];

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            SystemSetting::setVal($key, $value);
        }

        return redirect()->route('super-admin.landing-page.edit')->with('success', 'Landing page configuration updated successfully.');
    }
}
