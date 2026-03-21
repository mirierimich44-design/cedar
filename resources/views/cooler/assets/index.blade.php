@extends('layouts.app')
@section('title', 'Cooler Assets')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Cooler Assets
        <small class="tw-text-sm tw-text-gray-600 tw-font-semibold">Manage SBC Kenya cooler inventory</small>
    </h1>
</section>

<section class="content">
    {{-- Status summary --}}
    <div class="row tw-mb-4">
        @foreach(['available' => ['success','Available'], 'deployed' => ['primary','Deployed'], 'under_maintenance' => ['warning','Maintenance'], 'retrieved' => ['default','Retrieved']] as $status => [$color, $label])
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-{{ $color }}"><i class="fa fa-snowflake-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $label }}</span>
                    <span class="info-box-number">{{ $statusCounts[$status] ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">All Cooler Assets</h3>
            @can('cooler.asset.create')
            <div class="box-tools">
                <a href="{{ route('cooler.assets.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Add Cooler
                </a>
            </div>
            @endcan
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="cooler-assets-table">
                <thead>
                    <tr>
                        <th>Asset #</th>
                        <th>Type</th>
                        <th>Serial #</th>
                        <th>Tag</th>
                        <th>Status</th>
                        <th>Dealer</th>
                        <th>Deployed</th>
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
    var table = $('#cooler-assets-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("cooler.assets.index") }}',
        columns: [
            { data: 'asset_number' },
            { data: 'asset_type' },
            { data: 'serial_number', defaultContent: '<em>—</em>' },
            { data: 'cooler_tag', defaultContent: '<em>—</em>' },
            { data: 'status_badge', orderable: false },
            { data: 'dealer', orderable: false },
            { data: 'deployment_date', defaultContent: '<em>—</em>' },
            { data: 'action', orderable: false },
        ]
    });

    // Delete
    $(document).on('click', '.btn-delete-cooler-asset', function () {
        if (!confirm('Delete this cooler asset?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: '/cooler/assets/' + id,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { table.ajax.reload(); }
        });
    });
});
</script>
@endsection
