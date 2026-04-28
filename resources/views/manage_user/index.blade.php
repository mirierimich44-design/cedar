@extends('layouts.app')
@section('title', __('user.users'))

@section('css')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
    .page-toolbar h1 { margin:0; font-size:22px; font-weight:700; color:#111827; }
    .page-toolbar small { display:block; font-size:13px; color:#6b7280; font-weight:400; margin-top:2px; }
    table.dataTable thead th {
        background:#f9fafb !important; color:#374151 !important; font-size:11px !important;
        font-weight:700 !important; text-transform:uppercase !important; letter-spacing:.4px !important;
        border-bottom:2px solid #e5e7eb !important; padding:10px 12px !important; white-space:nowrap;
    }
    table.dataTable tbody tr:hover td { background:#f0f4ff !important; }
    table.dataTable tbody td { font-size:13px; color:#374151; padding:10px 12px !important; vertical-align:middle !important; border-bottom:1px solid #f3f4f6 !important; }
    .role-badge {
        display:inline-block; padding:2px 10px; border-radius:20px;
        font-size:11px; font-weight:600;
        background:#ede9fe; color:#5b21b6;
    }
</style>
@endsection

@section('content')

<section class="content-header">
    <div class="page-toolbar">
        <div>
            <h1>@lang('user.users')</h1>
            <small>@lang('user.manage_users')</small>
        </div>
        @can('user.create')
            <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-lg"
               href="{{ action([\App\Http\Controllers\ManageUserController::class, 'create']) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                </svg>
                @lang('messages.add')
            </a>
        @endcan
    </div>
</section>

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
