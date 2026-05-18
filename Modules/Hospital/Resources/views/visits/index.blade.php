@extends('layouts.app')
@section('title', "Today's Visits")

@section('content')
<section class="content-header">
    <h1><i class="fa fa-calendar-check-o"></i> Today's Visits
        <small>{{ \Carbon\Carbon::today()->format('d M Y') }}</small>
    </h1>
    <ol class="breadcrumb">
        <li class="active">Today's Visits</li>
    </ol>
</section>

<section class="content">

    {{-- Pipeline Status Counters --}}
    <div class="row" style="margin-bottom:10px;">
        @php
        $pipelineStages = [
            'triage'       => ['label'=>'Triage',       'color'=>'#17a2b8', 'icon'=>'fa-stethoscope'],
            'consultation' => ['label'=>'Consultation',  'color'=>'#007bff', 'icon'=>'fa-user-md'],
            'lab'          => ['label'=>'Lab',           'color'=>'#ffc107', 'icon'=>'fa-flask'],
            'pharmacy'     => ['label'=>'Pharmacy',      'color'=>'#6c757d', 'icon'=>'fa-pills'],
            'discharged'   => ['label'=>'Discharged',    'color'=>'#28a745', 'icon'=>'fa-check-circle'],
            'admitted'     => ['label'=>'Admitted',      'color'=>'#343a40', 'icon'=>'fa-bed'],
            'deceased'     => ['label'=>'Deceased',      'color'=>'#dc3545', 'icon'=>'fa-times-circle'],
        ];
        @endphp
        @foreach($pipelineStages as $key => $stage)
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2" style="margin-bottom:10px;">
            <div class="small-box" style="background:{{ $stage['color'] }};color:#fff;margin-bottom:0;padding:12px;">
                <div class="inner" style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h3 style="margin:0;font-size:2rem;font-weight:700;">{{ $counts[$key] ?? 0 }}</h3>
                        <p style="margin:0;font-size:12px;">{{ $stage['label'] }}</p>
                    </div>
                    <i class="fa {{ $stage['icon'] }}" style="font-size:2rem;opacity:0.5;"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list"></i> Visit Queue</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('hospital.visits.create') }}" class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i> Register Visit
                </a>
            </div>
        </div>
        <div class="box-body">
            <table id="visits-table" class="table table-striped table-bordered table-hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>Visit No.</th>
                        <th>Patient</th>
                        <th>Patient No.</th>
                        <th>Type</th>
                        <th>Time In</th>
                        <th>Triage</th>
                        <th>Status</th>
                        <th>Doctor</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    $('#visits-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('hospital.visits.index') }}',
        columns: [
            { data: 'visit_no',      name: 'visit_no' },
            { data: 'patient_name',  name: 'patient_name',  orderable: false },
            { data: 'patient_no',    name: 'patient_no',    orderable: false },
            { data: 'visit_type',    name: 'visit_type',    render: function(d){ return d ? (d.charAt(0).toUpperCase() + d.slice(1)) : '-'; } },
            { data: 'visited_at',    name: 'visited_at',    render: function(d){ return d ? moment(d).format('HH:mm') : '-'; } },
            { data: 'triage_badge',  name: 'triage_category', orderable: false },
            { data: 'status_badge',  name: 'status',        orderable: false },
            { data: 'assigned_doctor', name: 'assigned_doctor', defaultContent: '-' },
            { data: 'action',        name: 'action',        orderable: false, searchable: false },
        ],
        order: [[4, 'asc']],
        pageLength: 25,
    });
});
</script>
@endsection
