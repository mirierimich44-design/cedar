@extends('layouts.app')
@section('title', __('user.roles'))

@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h1>@lang('user.roles')</h1>
                    <p class="pg-subtitle">@lang('user.manage_roles') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('roles.create')
                    <a class="pg-add-btn"
                        href="{{ action([\App\Http\Controllers\RoleController::class, 'create']) }}">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('user.all_roles')])
            @can('roles.view')
                <table class="table table-bordered table-striped" id="roles_table">
                    <thead>
                        <tr>
                            <th>@lang('user.roles')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            @endcan
        @endcomponent
    </section>

</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var roles_table = $('#roles_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            ajax: '/roles',
            buttons: [],
            columnDefs: [{
                "targets": 1,
                "orderable": false,
                "searchable": false
            }]
        });
        $(document).on('click', 'button.delete_role_button', function() {
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_role,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                roles_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
