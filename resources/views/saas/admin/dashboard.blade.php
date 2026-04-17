@extends('layouts.app')
@section('title', 'SaaS Dashboard')
@section('content')
<section class="content-header">
    <h1 class="tw-text-2xl tw-font-bold">SaaS Dashboard</h1>
</section>
<section class="content">

<div class="row" style="margin-bottom:24px;">
    <div class="col-md-3 col-sm-6"><div class="info-box bg-teal"><span class="info-box-icon"><i class="fas fa-check-circle"></i></span><div class="info-box-content"><span class="info-box-text">Active</span><span class="info-box-number">{{ $stats['active'] }}</span></div></div></div>
    <div class="col-md-3 col-sm-6"><div class="info-box bg-yellow"><span class="info-box-icon"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Grace Period</span><span class="info-box-number">{{ $stats['grace'] }}</span></div></div></div>
    <div class="col-md-3 col-sm-6"><div class="info-box bg-red"><span class="info-box-icon"><i class="fas fa-ban"></i></span><div class="info-box-content"><span class="info-box-text">Suspended</span><span class="info-box-number">{{ $stats['suspended'] }}</span></div></div></div>
    <div class="col-md-3 col-sm-6"><div class="info-box bg-green"><span class="info-box-icon"><i class="fas fa-money-bill"></i></span><div class="info-box-content"><span class="info-box-text">Total Revenue</span><span class="info-box-number">KES {{ number_format($stats['revenue'], 0) }}</span></div></div></div>
</div>

<div class="row">
    <div class="col-md-8">
        @component('components.widget', ['header' => 'Recent Subscriptions'])
        <table class="table table-bordered table-hover">
            <thead><tr><th>Business</th><th>Cycle</th><th>Amount</th><th>Status</th><th>Expires</th></tr></thead>
            <tbody>
            @foreach($recent as $sub)
            <tr>
                <td>{{ optional($sub->business)->name ?? '—' }}</td>
                <td><span class="label label-info">{{ $sub->billing_cycle }}</span></td>
                <td>KES {{ number_format($sub->total_amount, 0) }}</td>
                <td>
                    @if($sub->status === 'active')<span class="label label-success">Active</span>
                    @elseif($sub->status === 'grace')<span class="label label-warning">Grace</span>
                    @elseif($sub->status === 'suspended')<span class="label label-danger">Suspended</span>
                    @else<span class="label label-default">{{ $sub->status }}</span>@endif
                </td>
                <td>{{ $sub->ends_at ? $sub->ends_at->format('d M Y') : '—' }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <a href="{{ route('saas.admin.subscriptions') }}" class="btn btn-default btn-sm">View All</a>
        @endcomponent
    </div>
    <div class="col-md-4">
        @component('components.widget', ['header' => 'Quick Links'])
        <div class="list-group">
            <a href="{{ route('saas.admin.features') }}" class="list-group-item"><i class="fas fa-puzzle-piece"></i> Manage Features</a>
            <a href="{{ route('saas.admin.bundles') }}"  class="list-group-item"><i class="fas fa-layer-group"></i> Manage Bundles</a>
            <a href="{{ route('saas.admin.subscriptions') }}" class="list-group-item"><i class="fas fa-credit-card"></i> All Subscriptions</a>
            <a href="{{ route('saas.admin.invoices') }}"  class="list-group-item"><i class="fas fa-file-invoice"></i> Invoices</a>
            <a href="{{ route('saas.pricing') }}" target="_blank" class="list-group-item"><i class="fas fa-external-link-alt"></i> View Pricing Page</a>
        </div>
        @endcomponent
    </div>
</div>
</section>
@endsection
