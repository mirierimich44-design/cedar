@extends('layouts.app')
@section('title', 'Maternity Management')

@section('content')
<section class="content-header">
    <h1>Maternity & ANC Dashboard</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Ongoing Pregnancies</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\MaternityController::class, 'createProfile']) }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> New Pregnancy Profile
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>LMP</th>
                        <th>EDD (Expected Date)</th>
                        <th>Gravida/Parity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profiles as $profile)
                        <tr>
                            <td>{{ $profile->patient->name }}</td>
                            <td>{{ $profile->lmp_date }}</td>
                            <td><strong>{{ $profile->edd_date }}</strong></td>
                            <td>G{{ $profile->gravida }} P{{ $profile->parity }}</td>
                            <td><span class="label bg-aqua">{{ ucfirst($profile->status) }}</span></td>
                            <td>
                                <a href="{{ action([\App\Http\Controllers\Hospital\MaternityController::class, 'showProfile'], [$profile->id]) }}" class="btn btn-success btn-xs">
                                    <i class="fa fa-eye"></i> View ANC File
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
