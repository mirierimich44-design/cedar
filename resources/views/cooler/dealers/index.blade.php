@extends('layouts.app')
@section('title', 'Cooler Customers')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Customers
        <small class="tw-text-sm tw-text-gray-600 tw-font-semibold">SBC Kenya cooler loan customers</small>
    </h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">All Customers</h3>
            @can('cooler.dealer.create')
            <div class="box-tools">
                <a href="{{ route('cooler.dealers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Register Customer
                </a>
            </div>
            @endcan
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="cooler-dealers-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Outlet</th>
                        <th>Channel</th>
                        <th>Phone</th>
                        <th>Area</th>
                        <th>Compliance</th>
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
    var table = $('#cooler-dealers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("cooler.dealers.index") }}',
        columns: [
            { data: 'name' },
            { data: 'outlet_name' },
            { data: 'channel' },
            { data: 'phone' },
            { data: 'area', defaultContent: '<em>—</em>' },
            { data: 'compliance', orderable: false },
            { data: 'status_badge', orderable: false },
            { data: 'action', orderable: false },
        ]
    });

    $(document).on('click', '.btn-delete-dealer', function () {
        if (!confirm('Delete this customer?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: '/cooler/dealers/' + id,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { table.ajax.reload(); }
        });
    });
});
</script>
@endsection
