@extends('layouts.app')
@section('title', 'Agreement #' . $agreement->id)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">
        Agreement #{{ $agreement->id }}
        {!! $agreement->status_badge !!}
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Agreement Details</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr><th>Dealer</th><td>{{ $agreement->dealer->name }} ({{ $agreement->dealer->outlet_name }})</td></tr>
                        <tr><th>Cooler</th><td>{{ $agreement->cooler->asset_number }} — {{ $agreement->cooler->asset_type }}</td></tr>
                        <tr><th>Date</th><td>{{ $agreement->agreement_date->format('d M Y') }}</td></tr>
                        <tr><th>Sales Target</th><td>KES {{ number_format($agreement->sales_volume_target) }}/month</td></tr>
                        <tr><th>Status</th><td>{!! $agreement->status_badge !!}</td></tr>
                        @if($agreement->termination_date)
                        <tr><th>Terminated</th><td>{{ $agreement->termination_date->format('d M Y') }}<br><small>{{ $agreement->termination_reason }}</small></td></tr>
                        @endif
                    </table>
                </div>
                <div class="box-footer tw-flex tw-gap-2">
                    <a href="{{ route('cooler.agreements.pdf', $agreement->id) }}" class="btn btn-sm btn-default" target="_blank">
                        <i class="fa fa-file-pdf-o"></i> Download PDF
                    </a>
                    @can('cooler.agreement.sign')
                    @if(!$agreement->isFullySigned())
                    <a href="{{ route('cooler.agreements.sign', $agreement->id) }}" class="btn btn-sm btn-warning">
                        <i class="fa fa-pen"></i> Capture Signatures ({{ $agreement->getSignedCount() }}/4)
                    </a>
                    @endif
                    @endcan
                    @can('cooler.agreement.terminate')
                    @if($agreement->status === 'active')
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#terminate-modal">
                        <i class="fa fa-times"></i> Terminate
                    </button>
                    @endif
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-md-7">
            {{-- Signature status --}}
            <div class="box box-success">
                <div class="box-header with-border"><h3 class="box-title">Signatures ({{ $agreement->getSignedCount() }}/4)</h3></div>
                <div class="box-body">
                    <div class="row">
                        @foreach([
                            ['label' => 'Company Legal', 'path' => $agreement->company_signature_path, 'name' => $agreement->company_signatory_name, 'at' => $agreement->company_signed_at],
                            ['label' => 'RSM/TSM',       'path' => $agreement->rsm_tsm_signature_path,  'name' => $agreement->rsm_tsm_signatory_name,  'at' => $agreement->rsm_tsm_signed_at],
                            ['label' => 'Dealer',        'path' => $agreement->dealer_signature_path,   'name' => $agreement->dealer_signatory_name,   'at' => $agreement->dealer_signed_at],
                            ['label' => 'Distributor',   'path' => $agreement->distributor_signature_path, 'name' => $agreement->distributor_signatory_name, 'at' => $agreement->distributor_signed_at],
                        ] as $sig)
                        <div class="col-md-6 tw-mb-4">
                            <div class="tw-border tw-rounded tw-p-3 {{ $sig['path'] ? 'tw-border-green-300 tw-bg-green-50' : 'tw-border-gray-200 tw-bg-gray-50' }}">
                                <p class="tw-font-semibold tw-text-sm tw-mb-1">{{ $sig['label'] }}</p>
                                @if($sig['path'] && file_exists(public_path($sig['path'])))
                                    <img src="{{ asset($sig['path']) }}" class="tw-max-h-16 tw-max-w-full tw-object-contain tw-border tw-bg-white tw-p-1 tw-rounded">
                                    <p class="tw-text-xs tw-mt-1 tw-text-gray-600">{{ $sig['name'] }}</p>
                                    <p class="tw-text-xs tw-text-gray-500">{{ $sig['at']?->format('d M Y H:i') }}</p>
                                @else
                                    <div class="tw-h-16 tw-flex tw-items-center tw-justify-center">
                                        <span class="tw-text-gray-400 tw-text-sm">Not yet signed</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Terminate modal --}}
    @can('cooler.agreement.terminate')
    <div class="modal fade" id="terminate-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title">Terminate Agreement</h4></div>
                {!! Form::open(['route' => ['cooler.agreements.terminate', $agreement->id], 'method' => 'POST']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('termination_reason', 'Reason for Termination *') !!}
                        {!! Form::textarea('termination_reason', null, ['class' => 'form-control', 'rows' => 3, 'required']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Terminate Agreement</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @endcan
</section>
@endsection
