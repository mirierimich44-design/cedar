@extends('layouts.app')
@section('title', 'IPD Management')

@section('content')
<section class="content-header">
    <h1>Inpatient Management (IPD)</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#beds_tab" data-toggle="tab">Bed Availability</a></li>
                    <li><a href="#admissions_tab" data-toggle="tab">Admitted Patients</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="beds_tab">
                        <div class="row">
                            @foreach($wards as $ward)
                                <div class="col-md-12">
                                    <h4>Ward: {{ $ward->name }}</h4>
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        @foreach($ward->beds as $bed)
                                            <div class="info-box" style="width: 150px; min-height: 80px; margin-bottom: 0;">
                                                <span class="info-box-icon {{ $bed->status == 'available' ? 'bg-green' : ($bed->status == 'occupied' ? 'bg-red' : 'bg-yellow') }}" style="width: 40px; height: 80px; line-height: 80px;">
                                                    <i class="fa fa-bed"></i>
                                                </span>
                                                <div class="info-box-content" style="margin-left: 40px; padding: 5px 10px;">
                                                    <span class="info-box-text">Bed {{ $bed->bed_number }}</span>
                                                    <span class="info-box-number" style="font-size: 12px;">{{ ucfirst($bed->status) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="admissions_tab">
                        <div class="box-header">
                            <div class="box-tools">
                                <a href="{{ action([\App\Http\Controllers\Hospital\HospitalController::class, 'createAdmission']) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Admit Patient
                                </a>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Ward/Bed</th>
                                    <th>Admission Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admissions as $admission)
                                    <tr>
                                        <td>{{ $admission->patient->name }}</td>
                                        <td>{{ $admission->bed->ward->name }} - Bed {{ $admission->bed->bed_number }}</td>
                                        <td>{{ $admission->admission_date }}</td>
                                        <td>{{ ucfirst($admission->status) }}</td>
                                        <td>
                                            <a href="{{ action([\App\Http\Controllers\Hospital\HospitalController::class, 'addDailyRecord'], [$admission->id]) }}" class="btn btn-info btn-xs">
                                                <i class="fa fa-pencil"></i> Daily Record
                                            </a>
                                            <a href="{{ action([\App\Http\Controllers\Hospital\HospitalController::class, 'dischargePatient'], [$admission->id]) }}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to discharge this patient?')">
                                                <i class="fa fa-sign-out"></i> Discharge
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
