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

        <h4 style="margin-top:20px;">Features</h4>
        <ul class="list-group">
            @foreach($subscription->features as $f)
            <li class="list-group-item" style="display:flex;justify-content:space-between;">
                <span><i class="fas fa-check-circle text-success"></i> {{ $f->name }}</span>
                <span class="text-muted">KES {{ number_format($f->pivot->price_locked, 0) }}</span>
            </li>
            @endforeach
        </ul>
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
