@extends('layouts.agent-portal')
@section('title', 'Customer — ' . $dealer->outlet_name)

@section('content')
<section class="content-header">
    <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-3">
        <div>
            <h1 class="tw-text-xl tw-font-bold tw-text-black">
                {{ $dealer->outlet_name }}
                {!! $dealer->status_badge !!}
            </h1>
            <p class="tw-text-sm tw-text-gray-500 tw-mt-0.5">{{ $dealer->name }} · {{ $dealer->phone }}</p>
        </div>
        <div class="tw-flex tw-gap-2">
            <a href="{{ route('cooler.agent.orders.create', ['customer_id' => $dealer->id]) }}"
               class="btn btn-success btn-sm"><i class="fa fa-shopping-cart"></i> Place Order</a>
            <a href="{{ route('cooler.agent.customers') }}" class="btn btn-default btn-sm">← Back</a>
        </div>
    </div>
</section>

<section class="content">
    <div class="row">
        {{-- Info card --}}
        <div class="col-md-4">
            <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden tw-mb-4">
                <div class="tw-px-4 tw-py-3" style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);">
                    <h3 class="tw-text-sm tw-font-bold tw-text-white">Customer Details</h3>
                </div>
                <table class="table tw-mb-0">
                    <tr><th class="tw-w-1/3">ID No.</th><td>{{ $dealer->id_number }}</td></tr>
                    <tr><th>KRA PIN</th><td>{{ $dealer->kra_pin ?: '—' }}</td></tr>
                    <tr><th>Phone</th><td>{{ $dealer->phone }}</td></tr>
                    <tr><th>Channel</th><td>{{ ucfirst($dealer->channel) }}</td></tr>
                    <tr><th>Location</th><td>{{ $dealer->full_address ?: '—' }}</td></tr>
                    <tr><th>Years in Biz</th><td>{{ $dealer->years_in_business ?? '—' }}</td></tr>
                    <tr>
                        <th>Compliance</th>
                        <td>
                            @php $s = $dealer->compliance_score; $c = $s >= 80 ? 'success' : ($s >= 50 ? 'warning' : 'danger'); @endphp
                            <span class="label label-{{ $c }}">{{ $s }}%</span>
                        </td>
                    </tr>
                    <tr><th>Brands</th><td>{{ implode(', ', $dealer->brands_stocked ?? []) ?: '—' }}</td></tr>
                </table>
            </div>
        </div>

        <div class="col-md-8">
            {{-- Assigned coolers --}}
            <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden tw-mb-4">
                <div class="tw-px-4 tw-py-3 tw-bg-gray-50 tw-border-b tw-border-gray-200">
                    <h3 class="tw-text-sm tw-font-bold tw-text-gray-700">Assigned Coolers</h3>
                </div>
                <div style="padding:8px 12px;">
                    @forelse($dealer->coolers as $cooler)
                    <div class="tw-flex tw-items-center tw-justify-between tw-py-2 tw-border-b tw-border-gray-100 last:tw-border-0">
                        <div>
                            <p class="tw-font-semibold tw-text-sm tw-text-gray-800">{{ $cooler->asset_number }}</p>
                            <p class="tw-text-xs tw-text-gray-500">{{ $cooler->asset_type }} · S/N: {{ $cooler->serial_number ?? '—' }}</p>
                        </div>
                        {!! $cooler->status_badge !!}
                    </div>
                    @empty
                    <p class="tw-text-sm tw-text-gray-400 tw-py-2">No coolers deployed to this customer.</p>
                    @endforelse
                </div>
            </div>

            {{-- Active agreement --}}
            @if($dealer->activeAgreement)
            <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden tw-mb-4">
                <div class="tw-px-4 tw-py-3 tw-bg-gray-50 tw-border-b tw-border-gray-200">
                    <h3 class="tw-text-sm tw-font-bold tw-text-gray-700">Active Agreement</h3>
                </div>
                <div class="tw-px-4 tw-py-3">
                    <p class="tw-text-sm tw-text-gray-700">
                        Cooler: <strong>{{ $dealer->activeAgreement->cooler->asset_number ?? '—' }}</strong>
                        · Signed: {{ $dealer->activeAgreement->agreement_date?->format('d M Y') ?? '—' }}
                        · Target: KES {{ number_format($dealer->activeAgreement->sales_volume_target) }}/mo
                    </p>
                </div>
            </div>
            @endif

            {{-- Retrieval history --}}
            <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden">
                <div class="tw-px-4 tw-py-3 tw-bg-gray-50 tw-border-b tw-border-gray-200 tw-flex tw-items-center tw-justify-between">
                    <h3 class="tw-text-sm tw-font-bold tw-text-gray-700">Retrieval History</h3>
                    <a href="{{ route('cooler.agent.retrievals') }}" class="tw-text-xs" style="color:var(--theme-main);">+ New Retrieval</a>
                </div>
                <div style="padding:8px 12px;">
                    @forelse($dealer->retrievals->take(5) as $r)
                    <div class="tw-flex tw-items-center tw-justify-between tw-py-2 tw-border-b tw-border-gray-100 last:tw-border-0">
                        <div>
                            <p class="tw-text-sm tw-text-gray-700">{{ $r->reason_label }}</p>
                            <p class="tw-text-xs tw-text-gray-500">{{ $r->retrieval_date?->format('d M Y') ?? '—' }}</p>
                        </div>
                        {!! $r->status_badge !!}
                    </div>
                    @empty
                    <p class="tw-text-sm tw-text-gray-400 tw-py-2">No retrievals on record.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</section>
@endsection
