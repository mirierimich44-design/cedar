@extends('layouts.app')
@section('title', 'Cooler Retrievals')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Retrievals</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Cooler Retrieval Records</h3>
            @can('cooler.retrieval.initiate')
            <div class="box-tools">
                <a href="{{ route('cooler.retrievals.create') }}" class="btn btn-danger btn-sm">
                    <i class="fa fa-truck"></i> Initiate Retrieval
                </a>
            </div>
            @endcan
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="retrievals-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Outlet</th>
                        <th>Cooler</th>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Status</th>
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
    $('#retrievals-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("cooler.retrievals.index") }}',
        columns: [
            { data: 'dealer_name' },
            { data: 'outlet_name' },
            { data: 'asset_number' },
            { data: 'retrieval_date' },
            { data: 'reason_label', orderable: false },
            { data: 'status_badge', orderable: false },
            { data: 'action', orderable: false },
        ]
    });
});
</script>
@endsection
