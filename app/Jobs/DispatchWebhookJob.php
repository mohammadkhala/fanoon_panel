<?php

namespace App\Jobs;

use App\Models\WebhookEndpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public WebhookEndpoint $endpoint,
        public string $event,
        public array $payload
    ) {}

    public function handle(): void
    {
        if (!$this->endpoint->is_active || !$this->endpoint->subscribesTo($this->event)) {
            return;
        }

        $headers = [
            'Content-Type' => 'application/json',
            'X-Webhook-Event' => $this->event,
            'User-Agent' => 'EliteVape-Webhook/1.0',
        ];

        if ($this->endpoint->secret) {
            $headers['X-Webhook-Signature'] = 'sha256=' . hash_hmac(
                'sha256',
                json_encode($this->payload),
                $this->endpoint->secret
            );
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->post($this->endpoint->url, $this->payload);

            if (!$response->successful()) {
                Log::warning('Webhook delivery failed', [
                    'endpoint_id' => $this->endpoint->id,
                    'event' => $this->event,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Webhook delivery error', [
                'endpoint_id' => $this->endpoint->id,
                'event' => $this->event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
