@extends('layouts.app')
@section('title', 'My Customers')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-2xl tw-font-bold tw-text-black">My Customers</h1>
</section>

<section class="content">
    @include('components.widget', [
        'title' => 'Customers I Manage',
        'tool'  => '<a href="' . route('cooler.agent.customers.create') . '" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Register Customer</a>',
    ])
    @slot('slot')
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
    @endslot

    @push('javascript')
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
    @endpush
</section>
@endsection
