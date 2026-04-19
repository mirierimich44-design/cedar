@extends('layouts.app')
@section('title', 'Outpatient Management')

@section('content')
<section class="content-header">
    <h1>Hospital - Outpatient Queue</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Today's Appointments</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\HospitalController::class, 'createAppointment']) }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Book Appointment
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Patient Name</th>
                        <th>Doctor</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_date }}</td>
                            <td>{{ $appointment->patient->name }}</td>
                            <td>{{ $appointment->doctor->first_name ?? 'Any' }}</td>
                            <td>{{ ucfirst($appointment->priority) }}</td>
                            <td>{{ ucfirst($appointment->status) }}</td>
                            <td>
                                @if($appointment->status != 'completed')
                                    <a href="{{ action([\App\Http\Controllers\Hospital\HospitalController::class, 'consultation'], [$appointment->id]) }}" class="btn btn-success btn-xs">
                                        <i class="fa fa-stethoscope"></i> Consultation
                                    </a>
                                    <a href="{{ action([\App\Http\Controllers\Hospital\DentalController::class, 'index'], [$appointment->patient_id]) }}" class="btn btn-info btn-xs">
                                        <i class="fa fa-smile-o"></i> Dental
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
