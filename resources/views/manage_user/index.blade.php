@extends('layouts.app')
@section('title', __('user.users'))

@section('css')
@parent
@include('layouts.partials.page_modern_css')
<style>
    .role-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: #ede9fe;
        color: #5b21b6;
    }
</style>
@endsection

@section('content')
<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h1>@lang('user.users')</h1>
                    <p class="pg-subtitle">@lang('user.manage_users') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('user.create')
                    <a class="pg-add-btn"
                        href="{{ action([\App\Http\Controllers\ManageUserController::class, 'create']) }}">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('user.all_users')])
            @can('user.view')
                <div class="table-responsive">
                    <table class="table table-hover" id="users_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>@lang('business.username')</th>
                                <th>@lang('user.name')</th>
                                <th>@lang('user.role')</th>
                                <th>@lang('business.email')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade user_modal" tabindex="-1" role="dialog"></div>
    </section>

</div>
@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var users_table = $('#users_table').DataTable({
        processing: true, serverSide: true, fixedHeader: false,
        ajax: '/users',
        columnDefs: [{ targets: [4], orderable: false, searchable: false }],
        columns: [
            { data: 'username'  },
            { data: 'full_name' },
            {
                data: 'role',
                render: function(data) {
                    return data ? '<span class="role-badge">' + data + '</span>' : '';
                }
            },
            { data: 'email'  },
            { data: 'action' }
        ]
    });

    $(document).on('click', 'button.delete_user_button', function() {
        swal({ title: LANG.sure, text: LANG.confirm_delete_user, icon: 'warning', buttons: true, dangerMode: true })
        .then(function(willDelete) {
            if (willDelete) {
                $.ajax({
                    method: 'DELETE', url: $('button.delete_user_button').data('href'), dataType: 'json',
                    success: function(result) {
                        if (result.success) { toastr.success(result.msg); users_table.ajax.reload(); }
                        else { toastr.error(result.msg); }
                    }
                });
            }
        });
    });
});
</script>
@endsection
