@extends('layouts.app')
@section('title', 'Patient Record')

@section('content')
<section class="content-header">
    <h1>{{ $patient->name }}
        <small>MRN: {{ optional($patient->patientDetails)->uhid_number ?? '—' }}</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-solid">
                <div class="box-header with-border"><h3 class="box-title">Demographics</h3></div>
                <div class="box-body">
                    <p><strong>Gender:</strong> {{ ucfirst(optional($patient->patientDetails)->gender ?? '—') }}</p>
                    <p><strong>DoB:</strong> {{ optional($patient->patientDetails)->date_of_birth ?? '—' }}</p>
                    <p><strong>Blood Group:</strong> {{ optional($patient->patientDetails)->blood_group ?? '—' }}</p>
                    <p><strong>Allergies:</strong> {{ optional($patient->patientDetails)->allergies ?? 'None recorded' }}</p>
                    <p><strong>Chronic:</strong> {{ optional($patient->patientDetails)->chronic_conditions ?? 'None' }}</p>
                    <p><strong>Mobile:</strong> {{ $patient->mobile }}</p>
                    <p><strong>Email:</strong> {{ $patient->email ?? '—' }}</p>
                    <hr>
                    <p><strong>Emergency:</strong> {{ optional($patient->patientDetails)->emergency_contact_name ?? '—' }}
                        ({{ optional($patient->patientDetails)->emergency_contact_number ?? '—' }})</p>
                    <p><strong>Insurance:</strong> {{ optional($patient->patientDetails)->insurance_provider ?? '—' }}
                        — {{ optional($patient->patientDetails)->insurance_policy_number ?? '—' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-solid">
                <div class="box-header with-border"><h3 class="box-title">Consultations</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <thead><tr><th>Date</th><th>Doctor</th><th>Diagnosis</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($patient->consultations as $c)
                            <tr>
                                <td>{{ $c->created_at->format('d M Y') }}</td>
                                <td>{{ optional($c->doctor)->first_name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($c->diagnosis, 80) }}</td>
                                <td><span class="label label-info">{{ $c->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No consultations yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-header with-border"><h3 class="box-title">Lab Requests</h3></div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <thead><tr><th>Date</th><th>Test</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($patient->labRequests as $lr)
                            <tr>
                                <td>{{ $lr->created_at->format('d M Y') }}</td>
                                <td>{{ optional($lr->test)->name ?? '—' }}</td>
                                <td>{{ $lr->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No lab requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
