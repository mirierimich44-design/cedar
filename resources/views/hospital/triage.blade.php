@extends('layouts.app')
@section('title', 'Triage — ' . $queue->token_number)

@section('content')
<section class="content-header">
    <h1>Triage <small>Record vitals &amp; move to Doctor</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('hospital.flow') }}">Patient Flow</a></li>
        <li><a href="{{ route('hospital.queue.index') }}">Queue</a></li>
        <li class="active">Triage</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            {{-- Patient Info Banner --}}
            <div class="callout callout-info" style="margin-bottom:15px;">
                <h4>
                    <span class="label label-primary" style="font-size:18px; margin-right:10px;">{{ $queue->token_number }}</span>
                    {{ $queue->patient->name }}
                </h4>
                <p class="text-muted" style="margin:0;">
                    <i class="fa fa-clock-o"></i> Arrived {{ $queue->created_at->diffForHumans() }}
                    &nbsp;&nbsp;
                    <i class="fa fa-map-marker"></i> Current stage: <strong>{{ ucfirst($queue->current_location) }}</strong>
                </p>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-heartbeat"></i> Record Vitals</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('hospital.queue.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Queue
                        </a>
                    </div>
                </div>

                {!! Form::open(['url' => route('hospital.storeTriage'), 'method' => 'post']) !!}
                {!! Form::hidden('queue_id', $queue->id) !!}

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><i class="fa fa-tachometer text-red"></i> BP (mmHg)</label>
                                {!! Form::text('vitals[bp]', null, ['class' => 'form-control', 'placeholder' => '120/80']) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><i class="fa fa-balance-scale text-blue"></i> Weight (kg)</label>
                                {!! Form::text('vitals[weight]', null, ['class' => 'form-control', 'placeholder' => '70']) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><i class="fa fa-thermometer-half text-orange"></i> Temp (°C)</label>
                                {!! Form::text('vitals[temp]', null, ['class' => 'form-control', 'placeholder' => '36.5']) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><i class="fa fa-cloud text-aqua"></i> SpO2 (%)</label>
                                {!! Form::text('vitals[spo2]', null, ['class' => 'form-control', 'placeholder' => '98']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><i class="fa fa-heartbeat text-green"></i> Pulse (bpm)</label>
                                {!! Form::text('vitals[pulse]', null, ['class' => 'form-control', 'placeholder' => '72']) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><i class="fa fa-male"></i> Height (cm)</label>
                                {!! Form::text('vitals[height]', null, ['class' => 'form-control', 'placeholder' => '170']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><i class="fa fa-pencil"></i> Triage Notes</label>
                                {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Chief complaint, urgent observations...']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer text-right">
                    <a href="{{ route('hospital.queue.index') }}" class="btn btn-default">Cancel</a>
                    &nbsp;
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa fa-arrow-circle-right"></i> Complete Triage &amp; Move to Doctor
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@endsection
