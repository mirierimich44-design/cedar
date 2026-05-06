@extends('layouts.app')
@section('title', __('barcode.barcodes'))

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
                    <i class="fas fa-barcode"></i>
                </div>
                <div>
                    <h1>@lang('barcode.barcodes')</h1>
                    <p class="pg-subtitle">@lang('barcode.manage_your_barcodes') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                <a class="pg-add-btn"
                    href="{{ action([\App\Http\Controllers\BarcodeController::class, 'create']) }}">
                    <i class="fas fa-plus"></i> @lang('barcode.add_new_setting')
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('barcode.all_your_barcode')])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="barcode_table">
                    <thead>
                        <tr>
                            <th>@lang('barcode.setting_name')</th>
                            <th>@lang('barcode.setting_description')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
    </section>

</div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var barcode_table = $('#barcode_table').DataTable({
            processing: true, serverSide: true, fixedHeader: false, buttons: [],
            ajax: '/barcodes', bPaginate: false,
            columnDefs: [{ "targets": 2, "orderable": false, "searchable": false }]
        });
        $(document).on('click', 'button.delete_barcode_button', function() {
            swal({ title: LANG.sure, text: LANG.confirm_delete_barcode, icon: "warning", buttons: true, dangerMode: true })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: "DELETE", url: $(this).data('href'), dataType: "json", data: $(this).serialize(),
                        success: function(result) {
                            if (result.success === true) { toastr.success(result.msg); barcode_table.ajax.reload(); }
                            else { toastr.error(result.msg); }
                        }
                    });
                }
            });
        });
        $(document).on('click', 'button.set_default', function() {
            $.ajax({
                method: "get", url: $(this).data('href'), dataType: "json", data: $(this).serialize(),
                success: function(result) {
                    if (result.success === true) { toastr.success(result.msg); barcode_table.ajax.reload(); }
                    else { toastr.error(result.msg); }
                }
            });
        });
    });
</script>
@endsection
