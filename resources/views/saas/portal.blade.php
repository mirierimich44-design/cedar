@extends('layouts.app')
@section('title', 'My Subscription')
@section('content')
<section class="content-header"><h1>My Subscription</h1></section>
<section class="content">
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

@if(!$subscription)
<div class="alert alert-warning">
    <strong>No active subscription found.</strong>
    <a href="{{ route('saas.pricing') }}" class="btn btn-primary btn-sm" style="margin-left:12px;">Build Your Plan</a>
</div>
@else
<div class="row">
    <div class="col-md-7">
        @component('components.widget', ['header' => 'Subscription Details'])
        <table class="table table-bordered">
            <tr><th style="width:35%">Status</th><td>
                @if($subscription->status === 'active')<span class="label label-success">Active</span>
                @elseif($subscription->status === 'grace')<span class="label label-warning">Grace Period</span>
                @elseif($subscription->status === 'suspended')<span class="label label-danger">Suspended</span>
                @else<span class="label label-info">{{ ucfirst($subscription->status) }}</span>@endif
            </td></tr>
            <tr><th>Billing Cycle</th><td>{{ ucfirst($subscription->billing_cycle) }}</td></tr>
            <tr><th>Total Amount</th><td><strong>KES {{ number_format($subscription->total_amount, 0) }}</strong></td></tr>
            <tr><th>Renews / Expires</th><td>{{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : '—' }}</td></tr>
            <tr><th>Days Remaining</th><td>{{ $subscription->daysLeft() }} days</td></tr>
        </table>

        <h4>Your Features</h4>
        <ul class="list-group">
            @foreach($subscription->features as $f)
            <li class="list-group-item">
                <i class="fas fa-check-circle text-success"></i>
                <strong>{{ $f->name }}</strong>
                <small class="text-muted pull-right">KES {{ number_format($f->pivot->price_locked, 0) }}/{{ $subscription->billing_cycle }}</small>
            </li>
            @endforeach
        </ul>

        <div style="margin-top:16px;">
            <a href="{{ route('saas.pricing') }}" class="btn btn-primary">Upgrade Plan</a>
        </div>
        @endcomponent
    </div>

    <div class="col-md-5">
        @component('components.widget', ['header' => 'Invoices'])
        @forelse($invoices as $inv)
        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f1f5f9;">
            <div>
                <strong>{{ $inv->invoice_no }}</strong><br>
                <small class="text-muted">{{ $inv->created_at->format('d M Y') }}</small>
            </div>
            <div style="text-align:right;">
                <div>KES {{ number_format($inv->amount, 0) }}</div>
                @if($inv->status === 'paid')
                    <span class="label label-success">Paid</span>
                @else
                    <span class="label label-warning">Unpaid</span>
                @endif
            </div>
        </div>
        @empty
        <p class="text-muted">No invoices yet.</p>
        @endforelse
        @endcomponent
    </div>
</div>
@endif
</section>
@endsection
