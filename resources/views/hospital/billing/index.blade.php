@extends('layouts.app')
@section('title', 'Hospital Billing')

@section('content')
<section class="content-header">
    <h1>Hospital Billing - Pending Invoices</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr>
                            <td>{{ $patient->name }}</td>
                            <td>{{ $patient->mobile }}</td>
                            <td><span class="label bg-orange">Pending Charges</span></td>
                            <td>
                                <a href="{{ action([\App\Http\Controllers\Hospital\HospitalBillingController::class, 'patientBill'], [$patient->id]) }}" class="btn btn-primary btn-xs">
                                    <i class="fa fa-calculator"></i> Generate Bill
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
