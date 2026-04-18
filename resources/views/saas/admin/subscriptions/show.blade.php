@extends('layouts.app')
@section('title', 'Subscription Details')
@section('content')
<section class="content-header">
    <h1>Subscription Details
        <a href="{{ route('saas.admin.subscriptions') }}" class="btn btn-default btn-sm pull-right">← Back</a>
    </h1>
</section>
<section class="content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row">
    <div class="col-md-7">
        @component('components.widget', ['header' => 'Subscription Info'])
        <table class="table table-bordered">
            <tr><th style="width:30%">Business</th><td>{{ optional($subscription->business)->name ?? '—' }}</td></tr>
            <tr><th>Status</th><td>
                @if($subscription->status === 'active')<span class="label label-success">Active</span>
                @elseif($subscription->status === 'grace')<span class="label label-warning">Grace</span>
                @elseif($subscription->status === 'suspended')<span class="label label-danger">Suspended</span>
                @elseif($subscription->status === 'pending')<span class="label label-info">Pending</span>
                @else<span class="label label-default">{{ $subscription->status }}</span>@endif
            </td></tr>
            <tr><th>Billing Cycle</th><td>{{ ucfirst($subscription->billing_cycle) }}</td></tr>
            <tr><th>Hosting</th><td>{{ $subscription->hosting_type === 'cloud' ? '☁️ Cloud' : '🖥️ Self-Hosted' }}</td></tr>
            <tr><th>Total Amount</th><td><strong>KES {{ number_format($subscription->total_amount, 0) }}</strong></td></tr>
            <tr><th>Started</th><td>{{ $subscription->starts_at ? $subscription->starts_at->format('d M Y') : '—' }}</td></tr>
            <tr><th>Expires</th><td>{{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : '—' }}</td></tr>
            <tr><th>Grace Until</th><td>{{ $subscription->grace_ends_at ? $subscription->grace_ends_at->format('d M Y') : '—' }}</td></tr>
        </table>

        <h4 style="margin-top:20px;">Enabled Features
            <small class="text-muted">({{ $subscription->features->count() }} active)</small>
        </h4>
        <ul class="list-group">
            @forelse($subscription->features as $f)
            <li class="list-group-item" style="display:flex;justify-content:space-between;align-items:center;">
                <span><i class="fas fa-check-circle text-success"></i> <strong>{{ $f->name }}</strong> <small class="text-muted">[{{ $f->category }}]</small></span>
                <span>
                    <span class="text-muted" style="margin-right:10px;">KES {{ number_format($f->pivot->price_locked, 0) }}</span>
                    <form method="POST" action="{{ route('saas.admin.subscriptions.feature.detach', [$subscription, $f]) }}" style="display:inline;" onsubmit="return confirm('Disable {{ $f->name }} for this client?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-danger"><i class="fas fa-times"></i> Disable</button>
                    </form>
                </span>
            </li>
            @empty
            <li class="list-group-item text-center text-muted">No features enabled yet.</li>
            @endforelse
        </ul>

        <h4 style="margin-top:20px;">Add a Feature to This Client</h4>
        @php
            $enabledIds  = $subscription->features->pluck('id')->toArray();
            $available   = \App\SaasFeature::where('is_active', true)
                            ->whereNotIn('id', $enabledIds)
                            ->orderBy('category')->orderBy('name')->get();
        @endphp
        @if($available->count())
        <form method="POST" action="{{ route('saas.admin.subscriptions.feature.attach', $subscription) }}" style="display:flex;gap:8px;">
            @csrf
            <select name="feature_id" class="form-control" required>
                <option value="">— Choose a feature to enable —</option>
                @foreach($available->groupBy('category') as $cat => $items)
                    <optgroup label="{{ ucfirst($cat) }}">
                        @foreach($items as $f)
                            <option value="{{ $f->id }}">{{ $f->name }} (KES {{ number_format($f->priceFor($subscription->billing_cycle ?? 'monthly'), 0) }}/{{ $subscription->billing_cycle }})</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <button class="btn btn-success" style="white-space:nowrap;"><i class="fas fa-plus"></i> Enable</button>
        </form>
        <p class="help-block" style="margin-top:6px;">Price auto-locks at the current rate for the client's billing cycle.</p>
        @else
        <p class="text-muted">All active features are already enabled for this client.</p>
        @endif
        @endcomponent
    </div>

    <div class="col-md-5">
        @component('components.widget', ['header' => 'Actions'])
        <form method="POST" action="{{ route('saas.admin.subscriptions.status', $subscription) }}" style="margin-bottom:16px;">
            @csrf
            <div class="form-group">
                <label>Change Status</label>
                <select name="status" class="form-control">
                    <option value="active"    {{ $subscription->status === 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="grace"     {{ $subscription->status === 'grace'     ? 'selected' : '' }}>Grace</option>
                    <option value="suspended" {{ $subscription->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending"   {{ $subscription->status === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ $subscription->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <button class="btn btn-warning btn-block">Update Status</button>
        </form>

        <form method="POST" action="{{ route('saas.admin.subscriptions.extend', $subscription) }}">
            @csrf
            <div class="form-group">
                <label>Extend by (days)</label>
                <input type="number" name="days" class="form-control" value="30" min="1">
            </div>
            <button class="btn btn-success btn-block">Extend Subscription</button>
        </form>
        @endcomponent

        @component('components.widget', ['header' => 'Invoices'])
        <table class="table table-condensed">
            <thead><tr><th>Invoice</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($subscription->invoices as $inv)
            <tr>
                <td>{{ $inv->invoice_no }}</td>
                <td>KES {{ number_format($inv->amount, 0) }}</td>
                <td>
                    @if($inv->status === 'paid')<span class="label label-success">Paid</span>
                    @elseif($inv->status === 'unpaid')<span class="label label-warning">Unpaid</span>
                    @else<span class="label label-danger">{{ $inv->status }}</span>@endif
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted">No invoices.</td></tr>
            @endforelse
            </tbody>
        </table>
        @endcomponent
    </div>
</div>
</section>
@endsection
