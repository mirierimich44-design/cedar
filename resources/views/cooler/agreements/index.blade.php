@extends('layouts.app')
@section('title', 'Cooler Agreements')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Agreements</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Cooler Loan Agreements</h3>
            @can('cooler.agreement.create')
            <div class="box-tools">
                <a href="{{ route('cooler.agreements.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> New Agreement
                </a>
            </div>
            @endcan
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="agreements-table">
                <thead>
                    <tr>
                        <th>Dealer</th>
                        <th>Outlet</th>
                        <th>Asset #</th>
                        <th>Date</th>
                        <th>Sales Target</th>
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
    $('#agreements-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("cooler.agreements.index") }}',
        columns: [
            { data: 'dealer_name' },
            { data: 'outlet_name' },
            { data: 'asset_number' },
            { data: 'agreement_date' },
            { data: 'sales_volume_target', render: function(d) { return d ? 'KES ' + parseFloat(d).toLocaleString() : '—'; } },
            { data: 'status_badge', orderable: false },
            { data: 'action', orderable: false },
        ]
    });
});
</script>
@endsection
