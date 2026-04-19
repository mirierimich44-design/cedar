@extends('layouts.app')
@section('title', 'MOH 705 Reports')

@section('content')
<section class="content-header">
    <h1>Ministry of Health (MOH) Reports</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">MOH 705A</h3>
                </div>
                <div class="box-body">
                    <p>Outpatient Summary Confirmation (Under 5 Years)</p>
                    <a href="{{ action([\App\Http\Controllers\Hospital\HospitalReportController::class, 'moh705A']) }}" class="btn btn-primary">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">MOH 705B</h3>
                </div>
                <div class="box-body">
                    <p>Outpatient Summary Confirmation (Over 5 Years)</p>
                    <a href="{{ action([\App\Http\Controllers\Hospital\HospitalReportController::class, 'moh705B']) }}" class="btn btn-success">View Report</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
