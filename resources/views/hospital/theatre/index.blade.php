@extends('layouts.app')
@section('title', 'Operating Theatre')

@section('content')
<section class="content-header">
    <h1>Operating Theatre (OT)</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Surgery Schedule</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\TheatreController::class, 'createBooking']) }}" class="btn btn-block btn-primary">
                    <i class="fa fa-plus"></i> Schedule Surgery
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Patient</th>
                        <th>Procedure</th>
                        <th>Theatre</th>
                        <th>Surgeon</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($booking->scheduled_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $booking->patient->name }}</td>
                        <td>{{ $booking->surgery->name }}</td>
                        <td>{{ $booking->theatre->name }}</td>
                        <td>Dr. {{ $booking->surgeon->first_name }}</td>
                        <td>
                            <span class="label @if($booking->status == 'completed') label-success @elseif($booking->status == 'scheduled') label-info @else label-warning @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td>
                            @if($booking->status != 'completed')
                            <a href="{{ action([\App\Http\Controllers\Hospital\TheatreController::class, 'editRecord'], [$booking->id]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-edit"></i> Update Record
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
