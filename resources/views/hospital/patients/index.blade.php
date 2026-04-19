@extends('layouts.app')
@section('title', 'Patients')

@section('content')
<section class="content-header">
    <h1>Patients
        <small>Medical record holders</small>
    </h1>
</section>

<section class="content">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Registered Patients</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('hospital.patients.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Register New Patient
                </a>
            </div>
        </div>
        <div class="box-body">
            @if(session('status'))
                <div class="alert alert-{{ session('status')['success'] ? 'success' : 'danger' }}">
                    {{ session('status')['msg'] }}
                </div>
            @endif

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>MRN</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>DoB</th>
                        <th>Blood Group</th>
                        <th>Mobile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $p)
                        <tr>
                            <td>{{ $p->uhid_number ?? '—' }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ ucfirst($p->gender ?? '—') }}</td>
                            <td>{{ $p->date_of_birth ?? '—' }}</td>
                            <td>{{ $p->blood_group ?? '—' }}</td>
                            <td>{{ $p->mobile }}</td>
                            <td>
                                <a href="{{ route('hospital.patients.show', $p->id) }}" class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('hospital.patient.timeline', $p->id) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-history"></i> Medical History
                                </a>
                                <a href="{{ route('hospital.queue.index') }}?patient_id={{ $p->id }}"
                                   class="btn btn-xs btn-success">
                                    <i class="fa fa-plus"></i> Add to Queue
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No patients yet — register your first patient.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $patients->links() }}
        </div>
    </div>
</section>
@endsection
