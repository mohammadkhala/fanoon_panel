<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderTrackingController extends Controller
{
    /**
     * Public tracking — search by reference, email, or phone.
     * Returns a single detailed order OR a list of order summaries.
     */
    public function index(Request $request): Response
    {
        $q     = trim((string) $request->query('q', ''));
        $order  = null;
        $orders = null;   // list when multiple found
        $error  = null;

        if ($q !== '') {
            $found = Order::with(['items', 'deliveryZone'])
                ->where(function ($builder) use ($q) {
                    $builder
                        // exact reference (case-insensitive)
                        ->where('reference', strtoupper($q))
                        // email on the order
                        ->orWhere('contact_email', $q)
                        // phone on the order
                        ->orWhere('contact_phone', $q)
                        // email of the linked user account
                        ->orWhereHas('user', fn ($u) => $u->where('email', $q));
                })
                ->latest()
                ->take(20)
                ->get();

            if ($found->isEmpty()) {
                $error = 'لم يتم العثور على طلبات مرتبطة بهذه المعلومات. تحقق وحاول مجدداً.';

            } elseif ($found->count() === 1) {
                $order = $this->formatDetail($found->first());

            } else {
                // Multiple orders — return summary list
                $orders = $found->map(fn ($o) => $this->formatSummary($o))->values();
            }
        }

        return Inertia::render('Storefront/TrackOrder', [
            'order'  => $order,
            'orders' => $orders,
            'error'  => $error,
            'q'      => $q,
        ]);
    }

    /* ── Formatters ─────────────────────────────────────── */

    private function formatDetail(Order $o): array
    {
        return [
            'reference'     => $o->reference,
            'status'        => $o->status,
            'status_label'  => $o->statusLabel(),
            'created_at'    => $o->created_at->format('Y-m-d'),
            'total'         => (float) $o->total,
            'items_count'   => $o->items->count(),
            'delivery_zone' => $o->deliveryZone?->name,
            'eta'           => $o->deliveryZone?->eta,
            'contact_name'  => $this->maskName($o->contact_name ?? ''),
            'contact_phone' => $this->maskPhone($o->contact_phone ?? ''),
        ];
    }

    private function formatSummary(Order $o): array
    {
        return [
            'reference'    => $o->reference,
            'status'       => $o->status,
            'status_label' => $o->statusLabel(),
            'created_at'   => $o->created_at->format('Y-m-d'),
            'total'        => (float) $o->total,
            'items_count'  => $o->items->count(),
        ];
    }

    /* ── Privacy maskers ──────────────────────────────── */

    private function maskName(string $name): string
    {
        if (!$name) return '';
        $parts = explode(' ', $name);
        return implode(' ', array_map(function (string $w) {
            $len = mb_strlen($w);
            if ($len <= 2) return $w;
            return mb_substr($w, 0, 1) . str_repeat('*', max(1, $len - 2)) . mb_substr($w, -1);
        }, $parts));
    }

    /** 0599123456 → 0599***456 */
    private function maskPhone(string $phone): string
    {
        $len = strlen($phone);
        if ($len < 6) return $phone;
        return substr($phone, 0, 4) . str_repeat('*', $len - 7) . substr($phone, -3);
    }
}
