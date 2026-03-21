@extends('layouts.app')
@section('title', 'Customer — ' . $dealer->outlet_name)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">
        {{ $dealer->outlet_name }}
        {!! $dealer->status_badge !!}
        <small class="tw-text-base tw-text-gray-600">{{ $dealer->name }}</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        {{-- Details card --}}
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Details</h3>
                    @can('cooler.dealer.update')
                    <div class="box-tools">
                        <a href="{{ route('cooler.dealers.edit', $dealer->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                    </div>
                    @endcan
                </div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr><th>ID Number</th><td>{{ $dealer->id_number }}</td></tr>
                        <tr><th>KRA PIN</th><td>{{ $dealer->kra_pin }}</td></tr>
                        <tr><th>Phone</th><td>{{ $dealer->phone }}</td></tr>
                        <tr><th>Channel</th><td>{{ ucfirst($dealer->channel) }}</td></tr>
                        <tr><th>Location</th><td>{{ $dealer->full_address ?: '—' }}</td></tr>
                        <tr><th>Years in Biz</th><td>{{ $dealer->years_in_business }}</td></tr>
                        <tr><th>Compliance</th><td>
                            @php $s = $dealer->compliance_score; $c = $s >= 80 ? 'success' : ($s >= 50 ? 'warning' : 'danger'); @endphp
                            <span class="label label-{{ $c }}">{{ $s }}%</span>
                        </td></tr>
                        <tr><th>Brands</th><td>{{ implode(', ', $dealer->brands_stocked ?? []) ?: '—' }}</td></tr>
                    </table>
                </div>
            </div>

            {{-- Active cooler --}}
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Coolers</h3></div>
                <div class="box-body">
                    @forelse($dealer->coolers as $cooler)
                    <div class="tw-flex tw-justify-between tw-items-center tw-py-1">
                        <span>{{ $cooler->asset_number }} — {{ $cooler->asset_type }}</span>
                        {!! $cooler->status_badge !!}
                    </div>
                    @empty
                    <p class="text-muted">No coolers assigned.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Documents gallery --}}
        <div class="col-md-8">
            <div class="box box-success">
                <div class="box-header with-border"><h3 class="box-title">Documents</h3></div>
                <div class="box-body">
                    @php
                        $docGroups = $dealer->documents->groupBy('document_type');
                        $personalTypes = ['id_copy', 'kra_pin_certificate', 'passport_photo'];
                        $legalTypes = ['county_business_permit', 'certificate_of_registration', 'certificate_of_incorporation', 'tax_certificate'];
                        $typeLabels = \App\CoolerDocument::$typeLabels;
                    @endphp

                    <div class="row">
                        @foreach(array_merge($personalTypes, $legalTypes) as $type)
                        @php $docs = $docGroups->get($type, collect()); $latest = $docs->last(); @endphp
                        <div class="col-md-4 col-sm-6 tw-mb-4">
                            <div class="tw-border tw-rounded-lg tw-p-3 tw-text-center {{ $latest ? 'tw-border-green-300 tw-bg-green-50' : 'tw-border-red-300 tw-bg-red-50' }}">
                                @if($latest)
                                    @if($latest->isImage())
                                        <a href="{{ route('cooler.documents.view', $latest->id) }}" target="_blank">
                                            <img src="{{ $latest->thumbnail_url }}" class="tw-w-full tw-h-20 tw-object-cover tw-rounded tw-mb-2">
                                        </a>
                                    @else
                                        <a href="{{ route('cooler.documents.view', $latest->id) }}" target="_blank">
                                            <i class="fa fa-file-pdf-o tw-text-4xl tw-text-red-500"></i>
                                        </a>
                                    @endif
                                    <p class="tw-text-xs tw-font-semibold tw-mt-1">{{ $typeLabels[$type] ?? $type }}</p>
                                    {!! $latest->status_badge !!}
                                    @if($latest->expires_at)
                                        <p class="tw-text-xs {{ $latest->isExpired() ? 'tw-text-red-600' : ($latest->isExpiringSoon() ? 'tw-text-orange-500' : 'tw-text-gray-500') }}">
                                            Exp: {{ $latest->expires_at->format('d M Y') }}
                                        </p>
                                    @endif
                                    <div class="tw-flex tw-gap-1 tw-justify-center tw-mt-2">
                                        @can('cooler.document.download')
                                        <a href="{{ route('cooler.documents.download', $latest->id) }}" class="btn btn-xs btn-default"><i class="fa fa-download"></i></a>
                                        @endcan
                                        @can('cooler.dealer.verify_docs')
                                        @if($latest->status === 'pending')
                                        <button class="btn btn-xs btn-success btn-verify-doc" data-id="{{ $latest->id }}"><i class="fa fa-check"></i></button>
                                        <button class="btn btn-xs btn-danger btn-reject-doc" data-id="{{ $latest->id }}"><i class="fa fa-times"></i></button>
                                        @endif
                                        @endcan
                                    </div>
                                @else
                                    <i class="fa fa-times-circle tw-text-4xl tw-text-red-400"></i>
                                    <p class="tw-text-xs tw-font-semibold tw-mt-2 tw-text-red-600">{{ $typeLabels[$type] ?? $type }}</p>
                                    <p class="tw-text-xs tw-text-red-500">Missing</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Active agreement --}}
            @if($dealer->activeAgreement)
            <div class="box box-warning">
                <div class="box-header with-border"><h3 class="box-title">Active Agreement</h3></div>
                <div class="box-body tw-flex tw-justify-between tw-items-center">
                    <div>
                        <strong>{{ $dealer->activeAgreement->cooler->asset_number ?? '—' }}</strong>
                        — Signed {{ $dealer->activeAgreement->agreement_date->format('d M Y') }}
                        — Target: KES {{ number_format($dealer->activeAgreement->sales_volume_target) }}/mo
                    </div>
                    <a href="{{ route('cooler.agreements.show', $dealer->activeAgreement->id) }}" class="btn btn-xs btn-warning">View Agreement</a>
                </div>
            </div>
            @else
            @can('cooler.agreement.create')
            <div class="box box-default">
                <div class="box-body tw-flex tw-justify-between tw-items-center">
                    <span class="text-muted">No active agreement.</span>
                    @if($dealer->hasRequiredDocuments())
                        <a href="{{ route('cooler.agreements.create') }}?dealer_id={{ $dealer->id }}" class="btn btn-sm btn-primary">Create Agreement</a>
                    @else
                        <span class="label label-warning">Complete document uploads first</span>
                    @endif
                </div>
            </div>
            @endcan
            @endif

            {{-- Compliance log --}}
            @if($dealer->complianceLogs->count())
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Recent Compliance Checks</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <thead><tr><th>Type</th><th>Status</th><th>Checked</th></tr></thead>
                        <tbody>
                            @foreach($dealer->complianceLogs as $log)
                            <tr>
                                <td>{{ \App\CoolerComplianceLog::$checkTypes[$log->check_type] ?? $log->check_type }}</td>
                                <td>{!! $log->status_badge !!}</td>
                                <td>{{ $log->checked_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    $(document).on('click', '.btn-verify-doc', function () {
        var id = $(this).data('id');
        $.post('/cooler/documents/' + id + '/verify', { _token: '{{ csrf_token() }}' }, function () {
            location.reload();
        });
    });

    $(document).on('click', '.btn-reject-doc', function () {
        var notes = prompt('Rejection reason:');
        if (!notes) return;
        var id = $(this).data('id');
        $.post('/cooler/documents/' + id + '/reject', { _token: '{{ csrf_token() }}', notes: notes }, function () {
            location.reload();
        });
    });
});
</script>
@endsection
