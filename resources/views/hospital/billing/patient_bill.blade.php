@extends('layouts.app')
@section('title', 'Patient Bill')

@section('content')
<section class="content-header">
    <h1>Generate Bill - {{ $patient->name }}</h1>
</section>

<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalBillingController::class, 'createInvoice']), 'method' => 'post']) !!}
    {!! Form::hidden('patient_id', $patient->id) !!}
    
    <div class="box box-primary">
        <div class="box-body">
            <h3>1. Consultations</h3>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unbilled_consultations as $con)
                        <tr>
                            <td>{{ $con->created_at->format('d/m/Y') }}</td>
                            <td>General Consultation</td>
                            <td>
                                {!! Form::number("consultations[$con->id]", 500, ['class' => 'form-control input-sm fee-input']) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h3>2. Laboratory Tests</h3>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Test Name</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unbilled_labs as $lab)
                        <tr>
                            <td>{{ $lab->created_at->format('d/m/Y') }}</td>
                            <td>{{ $lab->test->name }}</td>
                            <td>
                                {!! Form::number("labs[$lab->id]", $lab->test->price, ['class' => 'form-control input-sm fee-input']) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h3>3. Inpatient (IPD) Charges</h3>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Admission Date</th>
                        <th>Ward/Bed</th>
                        <th>Days</th>
                        <th>Daily Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unbilled_admissions as $adm)
                        @php
                            $start = \Carbon\Carbon::parse($adm->admission_date);
                            $end = $adm->discharge_date ? \Carbon\Carbon::parse($adm->discharge_date) : \Carbon\Carbon::now();
                            $days = max(1, $start->diffInDays($end));
                        @endphp
                        <tr>
                            <td>{{ $start->format('d/m/Y') }}</td>
                            <td>{{ $adm->bed->ward->name }} - Bed {{ $adm->bed->bed_number }}</td>
                            <td>
                                {!! Form::number("admissions[$adm->id][days]", $days, ['class' => 'form-control input-sm fee-input']) !!}
                            </td>
                            <td>
                                {!! Form::number("admissions[$adm->id][rate]", $adm->bed->daily_rate, ['class' => 'form-control input-sm fee-input']) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h3>4. Dental Procedures</h3>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Procedure</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unbilled_dental as $den)
                        <tr>
                            <td>{{ $den->created_at->format('d/m/Y') }}</td>
                            <td>{{ $den->procedure_name }}</td>
                            <td>
                                {!! Form::number("dental[$den->id]", $den->price, ['class' => 'form-control input-sm fee-input']) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            <div class="row">
                <div class="col-sm-6">
                    @if($patient->patientDetails && $patient->patientDetails->insurer)
                        <div class="well well-sm">
                            <h4>Insurance Details</h4>
                            <p><strong>Insurer:</strong> {{ $patient->patientDetails->insurer->name }}</p>
                            <p><strong>Scheme:</strong> {{ $patient->patientDetails->insuranceScheme->name ?? 'N/A' }}</p>
                            <p><strong>Card No:</strong> {{ $patient->patientDetails->insurance_card_number }}</p>
                            <div class="form-group">
                                {!! Form::label('insurance_coverage', 'Amount Covered by Insurance:') !!}
                                {!! Form::number('insurance_coverage', 0, ['class' => 'form-control', 'id' => 'insurance_coverage', 'step' => '0.01']) !!}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-sm-6 text-right">
                    <button type="submit" class="btn btn-success btn-lg">Generate Final Invoice</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection
