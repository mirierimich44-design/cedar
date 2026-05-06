@extends('layouts.app')
@section('title', __('lang_v1.selling_price_group'))

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
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h1>@lang('lang_v1.selling_price_group')</h1>
                    <p class="pg-subtitle">@lang('lang_v1.all_selling_price_group') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                <a class="pg-add-btn btn-modal"
                    data-href="{{ action([\App\Http\Controllers\SellingPriceGroupController::class, 'create']) }}"
                    data-container=".view_modal">
                    <i class="fas fa-plus"></i> @lang('messages.add')
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @if (session('notification') || !empty($notification))
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        @if (!empty($notification['msg']))
                            {{ $notification['msg'] }}
                        @elseif(session('notification.msg'))
                            {{ session('notification.msg') }}
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @component('components.widget', [
            'class' => 'box-primary',
            'title' => __('lang_v1.all_selling_price_group'),
            'help_text' => __('lang_v1.selling_price_help_text'),
        ])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="selling_price_group_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.name')</th>
                            <th>@lang('lang_v1.description')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade brands_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    </section>

</div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var selling_price_group_table = $('#selling_price_group_table').DataTable({
            processing: true, serverSide: true, fixedHeader: false,
            ajax: '/selling-price-group',
            columnDefs: [{ "targets": 2, "orderable": false, "searchable": false }]
        });

        $(document).on('submit', 'form#selling_price_group_form', function(e) {
            e.preventDefault();
            $.ajax({
                method: "POST", url: $(this).attr("action"), dataType: "json", data: $(this).serialize(),
                success: function(result) {
                    if (result.success == true) { $('div.view_modal').modal('hide'); toastr.success(result.msg); selling_price_group_table.ajax.reload(); }
                    else { toastr.error(result.msg); }
                }
            });
        });

        $(document).on('click', 'button.delete_spg_button', function() {
            swal({ title: LANG.sure, icon: "warning", buttons: true, dangerMode: true })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: "DELETE", url: $(this).data('href'), dataType: "json", data: $(this).serialize(),
                        success: function(result) {
                            if (result.success == true) { toastr.success(result.msg); selling_price_group_table.ajax.reload(); }
                            else { toastr.error(result.msg); }
                        }
                    });
                }
            });
        });

        $(document).on('click', 'button.activate_deactivate_spg', function() {
            $.ajax({
                url: $(this).data('href'), dataType: "json",
                success: function(result) {
                    if (result.success == true) { toastr.success(result.msg); selling_price_group_table.ajax.reload(); }
                    else { toastr.error(result.msg); }
                }
            });
        });
    });
</script>
@endsection
