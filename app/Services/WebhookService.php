<?php

namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookDeliveryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Dispatch event payload to all subscribed and active webhooks.
     */
    public function dispatch(string $eventType, array $payload): void
    {
        // Resolve active tenant school
        if (!app()->bound('tenant') || app('tenant') === null) {
            return;
        }

        $schoolId = app('tenant')->id;

        // Fetch active webhooks for school
        $webhooks = Webhook::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            // Check if webhook is subscribed to event
            $subscribed = $webhook->subscribed_events;
            if (is_string($subscribed)) {
                $subscribed = json_decode($subscribed, true) ?: [];
            }

            if (!in_array($eventType, $subscribed)) {
                continue;
            }

            // Create Delivery Log entry
            $log = WebhookDeliveryLog::create([
                'webhook_id' => $webhook->id,
                'event_type' => $eventType,
                'payload' => $payload, // Model casting will handle serialization if cast as array/json
                'attempt' => 1,
            ]);

            // Fire Webhook Request
            $this->fireRequest($webhook, $log, $payload, $eventType);
        }
    }

    /**
     * Fire the HTTP POST request to the webhook endpoint.
     */
    protected function fireRequest(Webhook $webhook, WebhookDeliveryLog $log, array $payload, string $eventType): void
    {
        $appName = config('app.name', 'EduLink');
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => $appName . '-Webhooks/1.0',
            'X-' . $appName . '-Event' => $eventType,
        ];

        // Generate signature if secret exists
        if ($webhook->secret) {
            $signature = hash_hmac('sha256', json_encode($payload), $webhook->secret);
            $headers['X-' . $appName . '-Signature'] = $signature;
        }

        try {
            // Fire HTTP Request with a short timeout to prevent blocking
            $response = Http::withHeaders($headers)
                ->timeout(3)
                ->post($webhook->url, $payload);

            $log->update([
                'response_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 5000), // Limit body size stored
                'delivered_at' => $response->successful() ? now() : null,
            ]);
        } catch (\Exception $e) {
            Log::error("Webhook delivery failed for hook #{$webhook->id}: " . $e->getMessage());

            $log->update([
                'response_status' => 500,
                'response_body' => 'HTTP request exception: ' . $e->getMessage(),
                'delivered_at' => null,
            ]);
        }
    }
}
