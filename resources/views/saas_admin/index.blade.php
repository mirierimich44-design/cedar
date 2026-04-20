@extends('layouts.app')
@section('title', 'SaaS Admin — All Businesses')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">SaaS Admin
        <small class="tw-text-sm tw-text-gray-600">Manage all businesses & features</small>
    </h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Businesses'])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="businesses_table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Business Name</th>
                        <th>Owner</th>
                        <th>Business Type</th>
                        <th>Active Modules</th>
                        <th>Registered</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
@endsection

@section('javascript')
<script>
$(function() {
    $('#businesses_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("saas-admin.index") }}',
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'id', width: '50px' },
            { data: 'name' },
            { data: 'owner_name' },
            { data: 'business_type' },
            { data: 'modules_count' },
            { data: 'created_at' },
        ]
    });
});
</script>
@endsection
