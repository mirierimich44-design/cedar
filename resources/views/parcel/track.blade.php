@extends('layouts.app')
@section('title', 'Track Parcel')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Track Parcel</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <!-- Search box -->
            <div class="box box-primary">
                <div class="box-header with-border bg-light-blue">
                    <h3 class="box-title"><i class="fa fa-search"></i> Enter Waybill Number</h3>
                </div>
                <div class="box-body">
                    {!! Form::open(['route' => 'parcels.track', 'method' => 'GET']) !!}
                    <div class="input-group input-group-lg">
                        {!! Form::text('waybill', request('waybill'), ['class' => 'form-control', 'placeholder' => 'e.g. WB-20260420-0001', 'style' => 'text-transform:uppercase']) !!}
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-search"></i> Track</button>
                        </span>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if(request()->filled('waybill'))
                @if($parcel)
                <!-- Parcel Found -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-box"></i> {{ $parcel->waybill_number }}
                            {!! $parcel->status_badge !!}
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <p><strong>From:</strong> {{ $parcel->from_town }}</p>
                                <p><strong>Sender:</strong> {{ $parcel->sender_name }}</p>
                            </div>
                            <div class="col-sm-4">
                                <p><strong>To:</strong> {{ $parcel->to_town }}</p>
                                <p><strong>Receiver:</strong> {{ $parcel->receiver_name }}</p>
                            </div>
                            <div class="col-sm-4">
                                <p><strong>Service:</strong> {{ ucfirst($parcel->service_type) }}</p>
                                <p><strong>Weight:</strong> {{ $parcel->weight_kg }} kg</p>
                                @if($parcel->expected_delivery_date)
                                <p><strong>Expected:</strong> {{ $parcel->expected_delivery_date->format('d M Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Progress bar -->
                        @php
                            $steps = ['booked', 'collected', 'in_transit', 'at_depot', 'out_for_delivery', 'delivered'];
                            $step_labels = ['Booked', 'Collected', 'In Transit', 'At Depot', 'Out for Delivery', 'Delivered'];
                            $current_step = array_search($parcel->status, $steps);
                            if ($current_step === false) $current_step = 0;
                        @endphp
                        <div class="tw-mt-4">
                            <div class="tw-flex tw-justify-between tw-mb-1">
                                @foreach($steps as $i => $step)
                                <div class="tw-text-center tw-flex-1">
                                    <div class="tw-w-8 tw-h-8 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-1
                                        {{ $i <= $current_step ? 'tw-bg-green-500 tw-text-white' : 'tw-bg-gray-200 tw-text-gray-500' }}">
                                        @if($i < $current_step)
                                            <i class="fa fa-check tw-text-xs"></i>
                                        @elseif($i == $current_step)
                                            <i class="fa fa-dot-circle tw-text-xs"></i>
                                        @else
                                            <span class="tw-text-xs">{{ $i + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="tw-text-xs {{ $i <= $current_step ? 'tw-font-bold tw-text-green-700' : 'tw-text-gray-400' }}">{{ $step_labels[$i] }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Timeline -->
                        <h4 class="tw-mt-4">Tracking History</h4>
                        <ul class="timeline">
                            @foreach($parcel->checkpoints as $cp)
                            <li>
                                <i class="fa fa-map-marker bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fa fa-clock"></i> {{ $cp->created_at->format('d M Y H:i') }}</span>
                                    <h3 class="timeline-header">{{ \App\ParcelCheckpoint::typeLabels()[$cp->checkpoint_type] ?? $cp->checkpoint_type }}
                                        <small>@ {{ $cp->location }}</small>
                                    </h3>
                                    @if($cp->status_note)
                                    <div class="timeline-body">{{ $cp->status_note }}</div>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                            @if($parcel->checkpoints->isEmpty())
                            <li><div class="timeline-item"><h3 class="timeline-header">No events recorded yet.</h3></div></li>
                            @endif
                        </ul>

                        @if($parcel->status === 'delivered')
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i>
                            <strong>Delivered!</strong> Received by {{ $parcel->delivered_to }} on {{ optional($parcel->actual_delivery_date)->format('d M Y H:i') }}
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    No parcel found with waybill number <strong>{{ request('waybill') }}</strong>.
                    Please check the number and try again.
                </div>
                @endif
            @endif
        </div>
    </div>
</section>
@endsection
