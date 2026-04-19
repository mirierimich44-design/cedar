@extends('layouts.app')

@section('title', 'Track Parcel - ' . $parcel->waybill_number)

@section('content')
<section class="content-header">
    <h1>Track Parcel: {{ $parcel->waybill_number }}</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Status:</strong> 
                            <span class="label label-info">{{ strtoupper($parcel->status) }}</span>
                        </div>
                        <div class="col-sm-6 text-right">
                            <strong>Payment:</strong> 
                            <span class="label label-{{ $parcel->payment_status == 'paid' ? 'success' : 'warning' }}">
                                {{ strtoupper($parcel->payment_status) }}
                            </span>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-sm-6">
                            <h4>Route Information</h4>
                            <p><strong>From:</strong> {{ $parcel->origin->name }} ({{ $parcel->origin->town }})</p>
                            <p><strong>To:</strong> {{ $parcel->destination->name }} ({{ $parcel->destination->town }})</p>
                        </div>
                        <div class="col-sm-6">
                            <h4>Parcel Details</h4>
                            <p><strong>Weight:</strong> {{ number_format($parcel->weight_kg, 2) }} kg</p>
                            <p><strong>Recipient:</strong> {{ $parcel->recipient_name }}</p>
                        </div>
                    </div>

                    <hr>

                    <h4>Tracking Timeline</h4>
                    <ul class="timeline">
                        @foreach($parcel->status_logs->sortByDesc('created_at') as $log)
                        <li>
                            <i class="fa fa-{{ $log->status == 'collected' ? 'check' : 'clock-o' }} bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> {{ $log->created_at->format('d M Y, h:i A') }}</span>
                                <h3 class="timeline-header">Parcel marked as <strong>{{ strtoupper($log->status) }}</strong></h3>
                                <div class="timeline-body">
                                    {{ $log->notes }}
                                    @if($log->station)
                                        <br><small class="text-muted">at {{ $log->station->name }}</small>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endforeach
                        <li>
                          <i class="fa fa-clock-o bg-gray"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
