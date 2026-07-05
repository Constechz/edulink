<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message to a specific recipient.
     *
     * @param string $to The phone number to send to.
     * @param string $message The content of the text message.
     * @param string|null $senderId Override default Sender ID configuration.
     * @return array Array containing 'success' (bool), 'reference' (string/null), and 'error' (string/null)
     */
    public function send($to, $message, $senderId = null)
    {
        $provider = SystemSetting::getVal('sms_gateway_provider', 'simulation');
        $apiKey = SystemSetting::getVal('sms_gateway_api_key', '');
        $defaultSenderId = SystemSetting::getVal('sms_gateway_sender_id', substr(config('app.name', 'EduLink'), 0, 11));
        
        $from = $senderId ?: $defaultSenderId;

        // Clean phone number format (ensure no spaces/plus signs, e.g. international format)
        $cleanTo = preg_replace('/[^0-9]/', '', $to);

        if ($provider === 'simulation') {
            Log::info("SMS SIMULATION [Sender: {$from} | Recipient: {$to}]: {$message}");
            return [
                'success' => true,
                'reference' => 'SIM-' . strtoupper(uniqid())
            ];
        }

        if ($provider === 'arkesel') {
            if (empty($apiKey)) {
                return [
                    'success' => false,
                    'error' => 'Arkesel SMS Gateway API Key is not configured.'
                ];
            }

            try {
                // Arkesel API request payload
                $response = Http::get('https://sms.arkesel.com/sms/api', [
                    'action' => 'send-sms',
                    'api_key' => $apiKey,
                    'to' => $cleanTo,
                    'from' => $from,
                    'sms' => $message
                ]);

                if ($response->successful()) {
                    $body = $response->json();
                    if (isset($body['status']) && $body['status'] === 'success') {
                        return [
                            'success' => true,
                            'reference' => $body['id'] ?? 'ARK-' . strtoupper(uniqid())
                        ];
                    }
                    return [
                        'success' => false,
                        'error' => $body['message'] ?? 'Arkesel response status error.'
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'HTTP Connection to Arkesel API failed with status code: ' . $response->status()
                ];
            } catch (\Exception $e) {
                Log::error("Arkesel SMS send failure: " . $e->getMessage());
                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        if ($provider === 'twilio') {
            // Twilio uses account SID / Auth Token, which can be stored in the api_key as a comma-separated value: "SID,Token,FromNumber"
            $credentials = explode(',', $apiKey);
            if (count($credentials) < 3) {
                return [
                    'success' => false,
                    'error' => 'Twilio SMS requires API Key in the format: "AccountSID,AuthToken,TwilioPhoneNumber"'
                ];
            }

            $sid = trim($credentials[0]);
            $token = trim($credentials[1]);
            $twilioFrom = trim($credentials[2]);

            // Ensure recipient phone has a leading '+' for global Twilio routing
            $twilioTo = str_starts_with($to, '+') ? $to : '+' . $cleanTo;

            try {
                $response = Http::withBasicAuth($sid, $token)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                        'To' => $twilioTo,
                        'From' => $twilioFrom,
                        'Body' => $message
                    ]);

                if ($response->successful()) {
                    $body = $response->json();
                    return [
                        'success' => true,
                        'reference' => $body['sid'] ?? 'TW-' . strtoupper(uniqid())
                    ];
                }

                $errorBody = $response->json();
                return [
                    'success' => false,
                    'error' => $errorBody['message'] ?? 'Twilio dispatch response failed.'
                ];
            } catch (\Exception $e) {
                Log::error("Twilio SMS send failure: " . $e->getMessage());
                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        if ($provider === 'bms') {
            if (empty($apiKey)) {
                return [
                    'success' => false,
                    'error' => 'BMS Africa SMS Gateway API Key is not configured.'
                ];
            }

            try {
                // BMS Africa / mNotify SMS REST API endpoint
                $response = Http::post("https://api.mnotify.com/api/sms/quick?key={$apiKey}", [
                    'recipient' => [$cleanTo],
                    'sender' => $from,
                    'message' => $message
                ]);

                if ($response->successful()) {
                    $body = $response->json();
                    if ((isset($body['status']) && $body['status'] === 'success') || (isset($body['code']) && $body['code'] === '2000')) {
                        return [
                            'success' => true,
                            'reference' => $body['summary']['_id'] ?? 'BMS-' . strtoupper(uniqid())
                        ];
                    }
                    return [
                        'success' => false,
                        'error' => $body['message'] ?? 'BMS Africa response status error.'
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'HTTP Connection to BMS Africa API failed with status code: ' . $response->status()
                ];
            } catch (\Exception $e) {
                Log::error("BMS Africa SMS send failure: " . $e->getMessage());
                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'Unknown SMS Gateway Provider configured.'
        ];
    }
}
