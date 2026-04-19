@extends('layouts.app')
@section('title', 'Pharmacy Dispensing Queue')

@section('content')
<section class="content-header">
    <h1>Pharmacy - Dispensing Queue</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Prescribed By</th>
                        <th>Pending Items</th>
                        <th>Last Update</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $patient_id => $items)
                        @php $first = $items->first(); @endphp
                        <tr>
                            <td><strong>{{ $first->patient->name }}</strong></td>
                            <td>{{ $first->doctor->first_name }}</td>
                            <td><span class="label label-info">{{ count($items) }} medicines</span></td>
                            <td>{{ $first->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ action([\App\Http\Controllers\Hospital\PharmacyController::class, 'dispense'], [$patient_id]) }}" class="btn btn-success btn-xs">
                                    <i class="fa fa-share-square-o"></i> Dispense Medicine
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
