<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookEndpoint;
use App\Services\WebhookService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WebhookController extends Controller
{
    /**
     * لا تضع abort() في __construct: أمر route:list (وما شابهه) يُنشئ المتحكم
     * لقراءة getMiddleware() فيتعطل عند إخفاء Webhooks.
     */
    private function ensureWebhooksEnabled(): void
    {
        if (config('feature_flags.hide_webhooks', true)) {
            abort(403, __('This feature is disabled.'));
        }
    }

    public function list(): View
    {
        $this->ensureWebhooksEnabled();
        $endpoints = WebhookEndpoint::orderByDesc('created_at')->paginate(20);
        return view('admin-views.webhook.list', compact('endpoints'));
    }

    public function create(): View
    {
        $this->ensureWebhooksEnabled();
        $events = [
            WebhookService::EVENT_ORDER_CREATED => translate('order_created'),
            WebhookService::EVENT_ORDER_STATUS_CHANGED => translate('order_status_changed'),
        ];
        return view('admin-views.webhook.form', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureWebhooksEnabled();
        $request->validate([
            'name' => 'nullable|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'in:' . WebhookService::EVENT_ORDER_CREATED . ',' . WebhookService::EVENT_ORDER_STATUS_CHANGED,
        ]);

        WebhookEndpoint::create([
            'name' => $request->name,
            'url' => $request->url,
            'events' => $request->events,
            'secret' => $request->filled('secret') ? $request->secret : Str::random(32),
            'is_active' => $request->boolean('is_active', true),
        ]);

        Toastr::success(translate('Webhook added successfully!'));
        return redirect()->route('admin.webhook.list');
    }

    public function edit(WebhookEndpoint $webhook): View
    {
        $this->ensureWebhooksEnabled();
        $events = [
            WebhookService::EVENT_ORDER_CREATED => translate('order_created'),
            WebhookService::EVENT_ORDER_STATUS_CHANGED => translate('order_status_changed'),
        ];
        return view('admin-views.webhook.form', compact('webhook', 'events'));
    }

    public function update(Request $request, WebhookEndpoint $webhook): RedirectResponse
    {
        $this->ensureWebhooksEnabled();
        $request->validate([
            'name' => 'nullable|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'in:' . WebhookService::EVENT_ORDER_CREATED . ',' . WebhookService::EVENT_ORDER_STATUS_CHANGED,
        ]);

        $webhook->update([
            'name' => $request->name,
            'url' => $request->url,
            'events' => $request->events,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->filled('secret')) {
            $webhook->update(['secret' => $request->secret]);
        }

        Toastr::success(translate('Webhook updated successfully!'));
        return redirect()->route('admin.webhook.list');
    }

    public function delete(WebhookEndpoint $webhook): RedirectResponse
    {
        $this->ensureWebhooksEnabled();
        $webhook->delete();
        Toastr::success(translate('Webhook removed!'));
        return back();
    }

    public function status(WebhookEndpoint $webhook): RedirectResponse
    {
        $this->ensureWebhooksEnabled();
        $webhook->update(['is_active' => !$webhook->is_active]);
        Toastr::success(translate('Webhook status updated!'));
        return back();
    }
}
