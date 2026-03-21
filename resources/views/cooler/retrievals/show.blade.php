@extends('layouts.app')
@section('title', 'Retrieval #' . $retrieval->id)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">
        Retrieval #{{ $retrieval->id }} {!! $retrieval->status_badge !!}
    </h1>
    <p class="tw-text-gray-600">{{ $retrieval->dealer->outlet_name }} — {{ $retrieval->cooler->asset_number }}</p>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Retrieval Details</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr><th>Dealer</th><td>{{ $retrieval->dealer->name }}</td></tr>
                        <tr><th>Outlet</th><td>{{ $retrieval->dealer->outlet_name }}</td></tr>
                        <tr><th>Cooler</th><td>{{ $retrieval->cooler->asset_number }} — {{ $retrieval->cooler->serial_number }}</td></tr>
                        <tr><th>Date</th><td>{{ $retrieval->retrieval_date->format('d M Y') }}</td></tr>
                        <tr><th>Reason</th><td>{{ $retrieval->reason_label }}</td></tr>
                        @if($retrieval->reason_notes)<tr><th>Notes</th><td>{{ $retrieval->reason_notes }}</td></tr>@endif
                        <tr><th>Authorized By</th><td>{{ $retrieval->authorized_staff_name }}</td></tr>
                        @if($retrieval->gps_coordinates)<tr><th>GPS</th><td><a href="https://maps.google.com/?q={{ $retrieval->gps_coordinates }}" target="_blank">{{ $retrieval->gps_coordinates }}</a></td></tr>@endif
                        <tr><th>Status</th><td>{!! $retrieval->status_badge !!}</td></tr>
                    </table>
                </div>
                <div class="box-footer tw-flex tw-gap-2">
                    @if($retrieval->status !== 'completed')
                    @can('cooler.retrieval.execute')
                    <a href="{{ route('cooler.retrievals.execute', $retrieval->id) }}" class="btn btn-sm btn-warning">
                        <i class="fa fa-truck"></i> Continue Execution
                    </a>
                    @endcan
                    @endif
                    <a href="{{ route('cooler.retrievals.letter', $retrieval->id) }}" class="btn btn-sm btn-default" target="_blank">
                        <i class="fa fa-file-pdf-o"></i> Retrieval Letter
                    </a>
                </div>
            </div>

            {{-- Customer signature --}}
            @if($retrieval->customer_signature_path)
            <div class="box box-success">
                <div class="box-header with-border"><h3 class="box-title">Customer Acknowledgement</h3></div>
                <div class="box-body">
                    <img src="{{ asset($retrieval->customer_signature_path) }}" class="tw-max-h-24 tw-border tw-rounded tw-bg-white tw-p-2">
                    <p class="tw-text-xs tw-text-gray-500 tw-mt-1">{{ $retrieval->acknowledgement_date?->format('d M Y H:i') }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-7">
            {{-- Photo evidence --}}
            @foreach([
                ['retrieval_photo_before', 'Before Photos', 'warning'],
                ['retrieval_photo_during', 'During Photos', 'default'],
                ['retrieval_photo_after',  'After Photos',  'success'],
            ] as [$type, $label, $color])
            @php $photos = $retrieval->documents->where('document_type', $type); @endphp
            <div class="box box-{{ $color }}">
                <div class="box-header with-border"><h3 class="box-title">{{ $label }} ({{ $photos->count() }})</h3></div>
                <div class="box-body">
                    @if($photos->count())
                    <div class="tw-flex tw-flex-wrap tw-gap-2">
                        @foreach($photos as $photo)
                        <a href="{{ $photo->url }}" target="_blank">
                            <img src="{{ $photo->thumbnail_url }}" class="tw-w-24 tw-h-24 tw-object-cover tw-rounded tw-border hover:tw-opacity-80">
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">No photos uploaded.</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
