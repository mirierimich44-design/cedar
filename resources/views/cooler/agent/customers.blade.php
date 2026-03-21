@extends('layouts.agent-portal')
@section('title', 'My Customers')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-2xl tw-font-bold tw-text-black">My Customers</h1>
</section>

<section class="content">
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-ring-1 tw-ring-gray-200 tw-overflow-hidden tw-mb-4">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-4 tw-py-3" style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);">
            <h3 class="tw-text-sm tw-font-bold tw-text-white">Customers I Manage</h3>
            <a href="{{ route('cooler.agent.customers.create') }}" class="btn btn-sm btn-light">
                <i class="fa fa-plus"></i> Register Customer
            </a>
        </div>
        <div style="padding:8px 12px;">
            <table class="table table-bordered table-striped" id="agent-customers-table">
                <thead>
                    <tr>
                        <th>Outlet Name</th>
                        <th>Contact Name</th>
                        <th>Phone</th>
                        <th>Channel</th>
                        <th>Area</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</section>
@endsection

@section('javascript')
<script>
$('#agent-customers-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('cooler.agent.customers') }}',
    columns: [
        { data: 'outlet_name' },
        { data: 'name' },
        { data: 'phone' },
        { data: 'channel' },
        { data: 'area', defaultContent: '—' },
        { data: 'status_badge', orderable: false },
        { data: 'action', orderable: false },
    ],
});
</script>
@endsection
