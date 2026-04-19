@extends('layouts.app')
@section('title', 'ANC Profile')

@section('content')
<section class="content-header">
    <h1>Antenatal Care - {{ $profile->patient->name }}</h1>
</section>

<section class="content">
    <div class="row">
        {{-- Profile Summary --}}
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Pregnancy Summary</h3>
                </div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr>
                            <th>LMP:</th>
                            <td>{{ $profile->lmp_date }}</td>
                        </tr>
                        <tr>
                            <th>EDD:</th>
                            <td><strong>{{ $profile->edd_date }}</strong></td>
                        </tr>
                        <tr>
                            <th>Gravida:</th>
                            <td>{{ $profile->gravida }}</td>
                        </tr>
                        <tr>
                            <th>Parity:</th>
                            <td>{{ $profile->parity }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="label bg-aqua">{{ ucfirst($profile->status) }}</span></td>
                        </tr>
                    </table>
                    <hr>
                    <strong>Medical History:</strong>
                    <p class="text-muted">{{ $profile->medical_history ?? 'None recorded' }}</p>
                </div>
            </div>

            {{-- Add ANC Visit Form --}}
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Record New ANC Visit</h3>
                </div>
                {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\MaternityController::class, 'storeAncVisit']), 'method' => 'post']) !!}
                {!! Form::hidden('pregnancy_profile_id', $profile->id) !!}
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('visit_date', 'Visit Date:*') !!}
                        {!! Form::date('visit_date', \Carbon::now()->toDateString(), ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                {!! Form::label('weight', 'Weight (kg):') !!}
                                {!! Form::text('weight', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                {!! Form::label('bp', 'BP:') !!}
                                {!! Form::text('bp', null, ['class' => 'form-control', 'placeholder' => '120/80']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('fundal_height', 'Fundal Height (cm):') !!}
                        {!! Form::text('fundal_height', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('fetal_presentation', 'Fetal Presentation:') !!}
                        {!! Form::select('fetal_presentation', ['Cephalic' => 'Cephalic', 'Breech' => 'Breech', 'Transverse' => 'Transverse'], null, ['class' => 'form-control', 'placeholder' => 'Select...']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('fetal_heart_rate', 'FHR (bpm):') !!}
                        {!! Form::text('fetal_heart_rate', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('tt_dose', 'TT Dose:') !!}
                        {!! Form::select('tt_dose', ['TT1' => 'TT1', 'TT2' => 'TT2', 'TT3' => 'TT3', 'TT4' => 'TT4', 'TT5' => 'TT5'], null, ['class' => 'form-control', 'placeholder' => 'Select...']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('notes', 'Notes / Plan:') !!}
                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-block">Save Visit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        {{-- Visit History --}}
        <div class="col-md-8">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">ANC Visit History</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr class="bg-gray">
                                <th>Date</th>
                                <th>Weight</th>
                                <th>BP</th>
                                <th>Fundal H.</th>
                                <th>Presentation</th>
                                <th>FHR</th>
                                <th>TT Dose</th>
                                <th>Doctor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profile->visits as $visit)
                                <tr>
                                    <td>{{ $visit->visit_date }}</td>
                                    <td>{{ $visit->weight }} kg</td>
                                    <td>{{ $visit->bp }}</td>
                                    <td>{{ $visit->fundal_height }} cm</td>
                                    <td>{{ $visit->fetal_presentation }}</td>
                                    <td>{{ $visit->fetal_heart_rate }}</td>
                                    <td>{{ $visit->tt_dose }}</td>
                                    <td>{{ $visit->doctor->first_name }}</td>
                                </tr>
                                @if($visit->notes)
                                    <tr class="info-row">
                                        <td colspan="8">
                                            <small><strong>Notes:</strong> {{ $visit->notes }}</small>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
