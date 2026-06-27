<?php

namespace App\Services;

use App\Jobs\DispatchWebhookJob;
use App\Models\Order;
use App\Models\WebhookEndpoint;

class WebhookService
{
    public const EVENT_ORDER_CREATED = 'order.created';
    public const EVENT_ORDER_STATUS_CHANGED = 'order.status_changed';

    public static function dispatchOrderCreated(Order $order): void
    {
        $payload = self::orderPayload($order);
        $payload['event'] = self::EVENT_ORDER_CREATED;

        self::dispatch(self::EVENT_ORDER_CREATED, $payload);
    }

    public static function dispatchOrderStatusChanged(Order $order, string $oldStatus, string $newStatus): void
    {
        $payload = self::orderPayload($order);
        $payload['event'] = self::EVENT_ORDER_STATUS_CHANGED;
        $payload['old_status'] = $oldStatus;
        $payload['new_status'] = $newStatus;

        self::dispatch(self::EVENT_ORDER_STATUS_CHANGED, $payload);
    }

    private static function dispatch(string $event, array $payload): void
    {
        WebhookEndpoint::where('is_active', true)
            ->get()
            ->filter(fn ($e) => $e->subscribesTo($event))
            ->each(fn ($e) => DispatchWebhookJob::dispatch($e, $event, $payload));
    }

    private static function orderPayload(Order $order): array
    {
        $order->load(['details']);
        return [
            'id' => $order->id,
            'order_amount' => (float) $order->order_amount,
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'created_at' => $order->created_at?->toIso8601String(),
            'details' => $order->details->map(fn ($d) => [
                'product_id' => $d->product_id,
                'quantity' => (int) $d->quantity,
                'price' => (float) $d->price,
            ])->values()->toArray(),
        ];
    }
}
