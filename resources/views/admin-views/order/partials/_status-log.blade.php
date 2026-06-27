{{-- سجل تغييرات حالة الطلب — يظهر دائماً --}}
@if(isset($order))
<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-header-title mb-0">
            <i class="tio-history"></i> {{ translate('order_status_log') ?: 'سجل التغييرات' }}
        </h5>
    </div>
    <div class="card-body p-0">
        @if($order->statusLogs && $order->statusLogs->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('changed_at') ?: 'التاريخ' }}</th>
                        <th>{{ translate('old_status') ?: 'من' }}</th>
                        <th>{{ translate('new_status') ?: 'إلى' }}</th>
                        <th>{{ translate('changed_by') ?: 'غيّر بواسطة' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->statusLogs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td><span class="badge badge-soft-secondary">{{ translate($log->old_status ?? '—') }}</span></td>
                        <td><span class="badge badge-soft-primary">{{ translate($log->new_status) }}</span></td>
                        <td>{{ $log->changed_by_display }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-4 text-center text-muted">
            <i class="tio-history font-size-2rem"></i>
            <p class="mb-0 mt-2">{{ translate('no_status_changes_yet') ?: 'لا توجد تغييرات مسجلة بعد. سيظهر السجل عند تغيير حالة الطلب.' }}</p>
        </div>
        @endif
    </div>
</div>
@endif
