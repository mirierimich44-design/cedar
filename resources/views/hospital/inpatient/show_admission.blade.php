@extends('layouts.app')
@section('title', 'Admission Details')

@section('content')
<section class="content-header">
    <h1>Inpatient: {{ $admission->patient->name }}</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Admission Info</h3>
                </div>
                <div class="box-body">
                    <p><strong>Ward:</strong> {{ $admission->ward->name }}</p>
                    <p><strong>Bed:</strong> {{ $admission->bed->name }}</p>
                    <p><strong>Admitted:</strong> {{ \Carbon\Carbon::parse($admission->admitted_at)->format('d M Y, H:i') }}</p>
                    <hr>
                    <a href="{{ action([\App\Http\Controllers\Hospital\InpatientController::class, 'discharge'], [$admission->id]) }}" class="btn btn-danger btn-block" onclick="return confirm('Are you sure?')">Discharge Patient</a>
                </div>
            </div>

            <!-- Fluid Balance Summary -->
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Fluid Balance (Today)</h3>
                </div>
                <div class="box-body">
                    @php 
                        $input = $admission->nursing_notes->where('noted_at', '>=', today())->sum('fluid_input_ml');
                        $output = $admission->nursing_notes->where('noted_at', '>=', today())->sum('fluid_output_ml');
                    @endphp
                    <p><strong>Total Input:</strong> {{ $input }} ml</p>
                    <p><strong>Total Output:</strong> {{ $output }} ml</p>
                    <p><strong>Net Balance:</strong> {{ $input - $output }} ml</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#nursing_notes" data-toggle="tab">Nursing Notes</a></li>
                    <li><a href="#daily_records" data-toggle="tab">Daily Records (Vitals)</a></li>
                    <li><a href="#add_note" data-toggle="tab">Add Note</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="nursing_notes">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Observation</th>
                                    <th>Input/Output</th>
                                    <th>Nurse</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admission->nursing_notes->sortByDesc('noted_at') as $note)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($note->noted_at)->format('d M, H:i') }}</td>
                                    <td>{{ $note->observation }}</td>
                                    <td>{{ $note->fluid_input_ml }} / {{ $note->fluid_output_ml }} ml</td>
                                    <td>{{ $note->nurse->first_name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="tab-pane" id="daily_records">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Vitals</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admission->daily_records->sortByDesc('created_at') as $dr)
                                <tr>
                                    <td>{{ $dr->created_at->format('d M, H:i') }}</td>
                                    <td>
                                        @if($dr->vitals)
                                            @foreach($dr->vitals as $k => $v)
                                                <small><strong>{{ strtoupper($k) }}:</strong> {{ $v }}</small><br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $dr->notes }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane" id="add_note">
                        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\InpatientController::class, 'addNursingNote']), 'method' => 'post']) !!}
                        {!! Form::hidden('admission_id', $admission->id) !!}
                        <div class="form-group">
                            {!! Form::label('observation', 'Observation:*') !!}
                            {!! Form::textarea('observation', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('fluid_input_ml', 'Fluid Input (ml):') !!}
                                    {!! Form::number('fluid_input_ml', 0, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('fluid_output_ml', 'Fluid Output (ml):') !!}
                                    {!! Form::number('fluid_output_ml', 0, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Note</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
