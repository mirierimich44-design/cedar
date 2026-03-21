@extends('layouts.app')
@section('title', 'Agent Portal')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-2xl tw-font-bold tw-text-black">
        Agent Portal
        <small class="tw-text-sm tw-text-gray-500 tw-font-normal">Welcome, {{ auth()->user()->first_name }}</small>
    </h1>
</section>

<section class="content">
    {{-- Mobile: large action buttons (hidden on desktop, shown on mobile) --}}
    <div class="tw-grid tw-grid-cols-2 tw-gap-3 tw-mb-4 lg:tw-hidden">
        <a href="{{ route('cooler.agent.customers.create') }}"
           class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-2 tw-rounded-2xl tw-py-5 tw-text-white tw-no-underline"
           style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);box-shadow:0 4px 12px rgba(0,0,0,0.15);">
            <i class="fa fa-user-plus" style="font-size:30px;"></i>
            <span class="tw-text-sm tw-font-bold tw-text-center tw-leading-tight">Register<br>Customer</span>
        </a>
        <a href="{{ route('cooler.agent.retrievals') }}"
           class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-2 tw-rounded-2xl tw-py-5 tw-text-white tw-no-underline"
           style="background:linear-gradient(135deg,#b45309 0%,#d97706 100%);box-shadow:0 4px 12px rgba(0,0,0,0.15);">
            <i class="fa fa-truck" style="font-size:30px;"></i>
            <span class="tw-text-sm tw-font-bold tw-text-center tw-leading-tight">Cooler<br>Retrieval</span>
        </a>
    </div>

    {{-- Stats row --}}
    <div class="tw-grid tw-grid-cols-2 xl:tw-grid-cols-4 tw-gap-4 tw-mb-6">
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider" style="color:var(--theme-main);">My Customers</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mt-1">{{ $stats['total_customers'] }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">{{ $stats['active_customers'] }} active</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider tw-text-yellow-600">Pending Retrievals</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mt-1">{{ $stats['pending_retrievals'] }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">initiated or in progress</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider tw-text-green-600">Orders Today</p>
            <p class="tw-text-3xl tw-font-bold tw-text-gray-900 tw-mt-1">{{ $stats['orders_today'] }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-mt-1">placed by you</p>
        </div>
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-p-4 tw-flex tw-flex-col tw-justify-between">
            <p class="tw-text-xs tw-font-semibold tw-uppercase tw-tracking-wider tw-text-gray-400">Quick Actions</p>
            <div class="tw-flex tw-flex-col tw-gap-2 tw-mt-2">
                <a href="{{ route('cooler.agent.customers.create') }}"
                   class="tw-inline-flex tw-items-center tw-gap-1 tw-text-sm tw-font-semibold tw-text-white tw-px-3 tw-py-1.5 tw-rounded-lg"
                   style="background:var(--theme-main);">
                    <i class="fa fa-user-plus"></i> Register Customer
                </a>
                <a href="{{ route('cooler.agent.retrievals') }}"
                   class="tw-inline-flex tw-items-center tw-gap-1 tw-text-sm tw-font-semibold tw-text-white tw-px-3 tw-py-1.5 tw-rounded-lg"
                   style="background:#d97706;">
                    <i class="fa fa-truck"></i> Cooler Retrieval
                </a>
                <a href="{{ route('cooler.agent.orders.create') }}"
                   class="tw-inline-flex tw-items-center tw-gap-1 tw-text-sm tw-font-semibold tw-text-white tw-px-3 tw-py-1.5 tw-rounded-lg tw-bg-green-600">
                    <i class="fa fa-shopping-cart"></i> Place Order
                </a>
            </div>
        </div>
    </div>

    <div class="tw-grid tw-grid-cols-1 xl:tw-grid-cols-3 tw-gap-5">
        {{-- Recent customers --}}
        <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden">
            <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between" style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);">
                <h3 class="tw-text-sm tw-font-bold tw-text-white">Recent Customers</h3>
                <a href="{{ route('cooler.agent.customers') }}" class="tw-text-xs tw-text-white/70 hover:tw-text-white">View all</a>
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
            <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between tw-bg-yellow-500">
                <h3 class="tw-text-sm tw-font-bold tw-text-white">Recent Retrievals</h3>
                <a href="{{ route('cooler.agent.retrievals') }}" class="tw-text-xs tw-text-white/70 hover:tw-text-white">View all</a>
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
            <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between tw-bg-green-600">
                <h3 class="tw-text-sm tw-font-bold tw-text-white">Recent Orders</h3>
                <a href="{{ route('cooler.agent.orders.create') }}" class="tw-text-xs tw-text-white/70 hover:tw-text-white">+ New</a>
            </div>
            <div class="tw-divide-y tw-divide-gray-100">
                @forelse($recentOrders as $o)
                    <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-justify-between">
                        <div>
                            <p class="tw-font-semibold tw-text-sm tw-text-gray-800">{{ $o->ref_no }}</p>
                            <p class="tw-text-xs tw-text-gray-500">KES {{ number_format($o->total_amount, 2) }}</p>
                        </div>
                        <span class="tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full tw-font-semibold {{ $o->payment_status === 'paid' ? 'tw-bg-green-100 tw-text-green-700' : 'tw-bg-yellow-100 tw-text-yellow-700' }}">
                            {{ ucfirst($o->payment_status) }}
                        </span>
                    </div>
                @empty
                    <p class="tw-px-4 tw-py-3 tw-text-sm tw-text-gray-400">No orders yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    @include('cooler.agent._mobile_nav')
</section>
@endsection
