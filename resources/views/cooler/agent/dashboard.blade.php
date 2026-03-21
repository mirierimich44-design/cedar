@extends('layouts.agent-portal')
@section('title', 'Agent Portal')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">
        Agent Portal
        <small class="tw-text-sm tw-text-gray-500 tw-font-normal">Welcome, {{ auth()->user()->first_name }}</small>
    </h1>
</section>

<section class="content">
    {{-- Primary action buttons --}}
    <div class="tw-grid tw-grid-cols-2 tw-gap-3 tw-mb-5">
        <a href="{{ route('cooler.agent.customers.create') }}"
           class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-2 tw-rounded-2xl tw-py-6 tw-text-white tw-no-underline"
           style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);box-shadow:0 4px 14px rgba(0,0,0,0.15);">
            <i class="fa fa-user-plus" style="font-size:32px;"></i>
            <span class="tw-text-sm tw-font-bold tw-text-center tw-leading-tight">Register<br>Customer</span>
        </a>
        <a href="{{ route('cooler.agent.retrievals') }}"
           class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-2 tw-rounded-2xl tw-py-6 tw-text-white tw-no-underline"
           style="background:linear-gradient(135deg,#b45309 0%,#d97706 100%);box-shadow:0 4px 14px rgba(0,0,0,0.15);">
            <i class="fa fa-truck" style="font-size:32px;"></i>
            <span class="tw-text-sm tw-font-bold tw-text-center tw-leading-tight">Cooler<br>Retrieval</span>
        </a>
    </div>

    {{-- Stats row --}}
    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-3 tw-mb-5">
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider" style="color:var(--theme-main);">My Customers</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mt-1">{{ $stats['total_customers'] }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">{{ $stats['active_customers'] }} active</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider tw-text-yellow-600">Retrievals</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mt-1">{{ $stats['pending_retrievals'] }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">pending</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider tw-text-green-600">Orders Today</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mt-1">{{ $stats['orders_today'] }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">placed by you</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider tw-text-gray-400">Active</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mt-1">{{ $stats['active_customers'] }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">customers</p>
        </div>
    </div>

    {{-- Recent panels --}}
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
        {{-- Recent customers --}}
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden">
            <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between"
                 style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);">
                <h3 class="tw-text-sm tw-font-bold tw-text-white">Recent Customers</h3>
                <a href="{{ route('cooler.agent.customers') }}" style="color:rgba(255,255,255,0.75);font-size:11px;text-decoration:none;">View all</a>
            </div>
            <div class="tw-divide-y tw-divide-gray-100">
                @forelse($recentCustomers as $c)
                    <div class="tw-px-4 tw-py-3">
                        <p class="tw-font-semibold tw-text-sm tw-text-gray-800">{{ $c->outlet_name }}</p>
                        <p class="tw-text-xs tw-text-gray-500">{{ $c->name }} · {{ $c->phone }}</p>
                    </div>
                @empty
                    <p class="tw-px-4 tw-py-3 tw-text-sm tw-text-gray-400">No customers yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent retrievals --}}
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden">
            <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between" style="background:#d97706;">
                <h3 class="tw-text-sm tw-font-bold tw-text-white">Recent Retrievals</h3>
                <a href="{{ route('cooler.agent.retrievals') }}" style="color:rgba(255,255,255,0.75);font-size:11px;text-decoration:none;">View all</a>
            </div>
            <div class="tw-divide-y tw-divide-gray-100">
                @forelse($recentRetrievals as $r)
                    <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between">
                        <div>
                            <p class="tw-font-semibold tw-text-sm tw-text-gray-800">{{ $r->cooler->asset_number ?? '—' }}</p>
                            <p class="tw-text-xs tw-text-gray-500">{{ $r->dealer->outlet_name ?? '—' }}</p>
                        </div>
                        {!! $r->status_badge !!}
                    </div>
                @empty
                    <p class="tw-px-4 tw-py-3 tw-text-sm tw-text-gray-400">No retrievals yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden">
            <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between" style="background:#059669;">
                <h3 class="tw-text-sm tw-font-bold tw-text-white">Recent Orders</h3>
                <a href="{{ route('cooler.agent.orders.create') }}" style="color:rgba(255,255,255,0.75);font-size:11px;text-decoration:none;">+ New</a>
            </div>
            <div class="tw-divide-y tw-divide-gray-100">
                @forelse($recentOrders as $o)
                    <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between">
                        <div>
                            <p class="tw-font-semibold tw-text-sm tw-text-gray-800">{{ $o->ref_no }}</p>
                            <p class="tw-text-xs tw-text-gray-500">KES {{ number_format($o->total_amount, 2) }}</p>
                        </div>
                        <span class="tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full tw-font-semibold"
                              style="{{ $o->payment_status === 'paid' ? 'background:#dcfce7;color:#166534;' : 'background:#fef9c3;color:#854d0e;' }}">
                            {{ ucfirst($o->payment_status) }}
                        </span>
                    </div>
                @empty
                    <p class="tw-px-4 tw-py-3 tw-text-sm tw-text-gray-400">No orders yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
