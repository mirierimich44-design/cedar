@extends('layouts.app')
@section('title', 'Parcel Stations')

@section('content')
<section class="content-header">
    <h1>Stations <small>Manage parcel hubs</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('parcel.index') }}">Parcels</a></li>
        <li class="active">Stations</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Add Station</h3></div>
                <div class="box-body">
                    <form method="POST" action="{{ route('parcel.stations.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Station Name <span class="text-red">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Nairobi CBD Hub" required>
                        </div>
                        <div class="form-group">
                            <label>Town <span class="text-red">*</span></label>
                            <input type="text" name="town" class="form-control" placeholder="e.g. Nairobi" required>
                        </div>
                        <div class="form-group">
                            <label>County</label>
                            <input type="text" name="county" class="form-control" placeholder="e.g. Nairobi County">
                        </div>
                        <div class="form-group">
                            <label>Contact / Phone</label>
                            <input type="text" name="contact" class="form-control" placeholder="0700 000 000">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Add Station</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">All Stations ({{ $stations->count() }})</h3></div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped">
                        <thead><tr><th>Name</th><th>Town</th><th>County</th><th>Contact</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($stations as $station)
                            <tr>
                                <td><strong>{{ $station->name }}</strong></td>
                                <td>{{ $station->town }}</td>
                                <td>{{ $station->county ?? '—' }}</td>
                                <td>{{ $station->contact ?? '—' }}</td>
                                <td><span class="label {{ $station->is_active ? 'label-success' : 'label-default' }}">{{ $station->is_active ? 'Active' : 'Inactive' }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No stations yet. Add one to get started.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
