@extends('layouts.app')
@section('title', 'SaaS Admin — All Businesses')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">SaaS Admin
        <small class="tw-text-sm tw-text-gray-600">Manage all businesses & features</small>
    </h1>
</section>

<section class="content">

    {{-- Platform Settings --}}
    <div class="box box-default" style="margin-bottom:20px;">
        <div class="box-header with-border"><h3 class="box-title">Platform Settings</h3></div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="tw-flex tw-items-center tw-gap-3 tw-cursor-pointer">
                        <input type="checkbox" id="allow_registration_toggle"
                            {{ \App\System::getProperty('allow_registration') !== '0' ? 'checked' : '' }}
                            style="width:18px;height:18px;">
                        <span><strong>Allow new business registration</strong><br>
                        <small class="text-muted">Show "Create Account" link on the login page.</small></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

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
    $('#allow_registration_toggle').on('change', function() {
        $.post('{{ route("saas.admin.setting") }}', {
            _token: '{{ csrf_token() }}',
            key: 'allow_registration',
            value: $(this).is(':checked') ? '1' : '0'
        }).done(function() {
            toastr.success('Setting saved.');
        }).fail(function() {
            toastr.error('Failed to save setting.');
        });
    });

    $('#businesses_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("saas.admin.business") }}',
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
