@extends('layouts.app')
@section('title', 'Patient — ' . $patient->full_name)

@section('content')
<section class="content-header">
    <h1>🏥 {{ $patient->full_name }}
        <small>{{ $patient->patient_no }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('hospital.patients.index') }}">Patients</a></li>
        <li class="active">{{ $patient->full_name }}</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        {{-- Patient Info Card --}}
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Patient Details</h3>
                    <div class="box-tools">
                        <a href="{{ route('hospital.patients.edit', $patient->id) }}" class="btn btn-xs btn-default">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-condensed" style="margin-bottom:0;">
                        <tr><th style="width:40%;">Patient No.</th><td><strong>{{ $patient->patient_no }}</strong></td></tr>
                        <tr><th>Name</th><td>{{ $patient->full_name }}</td></tr>
                        <tr><th>DOB</th><td>{{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->format('d M Y') : '-' }}</td></tr>
                        <tr><th>Gender</th><td>{{ $patient->gender }}</td></tr>
                        <tr><th>Phone</th><td>{{ $patient->phone }}</td></tr>
                        <tr><th>Email</th><td>{{ $patient->email ?: '-' }}</td></tr>
                        <tr><th>Blood Group</th><td>
                            @if($patient->blood_group)
                                <span class="label label-danger">{{ $patient->blood_group }}</span>
                            @else - @endif
                        </td></tr>
                        <tr><th>Allergies</th><td><span class="text-danger">{{ $patient->allergies ?: 'None recorded' }}</span></td></tr>
                        <tr><th>Emergency Contact</th><td>{{ $patient->emergency_contact_name }}<br><small>{{ $patient->emergency_contact_phone }}</small></td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Visit History --}}
        <div class="col-md-8">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Visit History</h3>
                    <div class="box-tools">
                        <a href="{{ route('hospital.visits.create') }}?patient_id={{ $patient->id }}" class="btn btn-sm btn-success">
                            <i class="fa fa-plus"></i> Register Visit
                        </a>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-condensed table-striped" id="visits-table">
                        <thead>
                            <tr>
                                <th>Visit No.</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Triage</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->visits->sortByDesc('visited_at') as $visit)
                            <tr>
                                <td><strong>{{ $visit->visit_no }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($visit->visited_at)->format('d M Y, H:i') }}</td>
                                <td>{{ ucfirst($visit->visit_type) }}</td>
                                <td>
                                    @php
                                        $triageColors = ['green'=>'success','yellow'=>'warning','orange'=>'warning','red'=>'danger','black'=>'default'];
                                        $tc = $triageColors[$visit->triage_category] ?? 'default';
                                    @endphp
                                    <span class="label label-{{ $tc }}">{{ ucfirst($visit->triage_category ?? 'N/A') }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = ['triage'=>'info','consultation'=>'primary','lab'=>'warning','pharmacy'=>'default','discharged'=>'success','admitted'=>'danger','deceased'=>'danger'];
                                        $sc = $statusColors[$visit->status] ?? 'default';
                                    @endphp
                                    <span class="label label-{{ $sc }}">{{ ucfirst($visit->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('hospital.visits.show', $visit->id) }}" class="btn btn-xs btn-info">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">No visits recorded</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
