@extends('layouts.app')
@section('title', __('barcode.barcodes'))


@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')

<!-- Content Header (Page header) -->

<div class="page-modern">

    <section class="content-header no-print"></section>

    <div class="pg-banner no-print">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h1>@lang('barcode.barcodes')</h1>
                    <p class="pg-subtitle">@lang('barcode.manage_your_barcodes') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>
<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang('barcode.all_your_barcode')</h3>
        	<div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\BarcodeController::class, 'create'])}}">
				<i class="fa fa-plus"></i> @lang('barcode.add_new_setting')</a>
            </div>
        </div>
        <div class="box-body">
        	<table class="table table-bordered table-striped" id="barcode_table">
        		<thead>
        			<tr>
        				<th>@lang('barcode.setting_name')</th>
						<th>@lang('barcode.setting_description')</th>
						<th>Action</th>
        			</tr>
        		</thead>
        	</table>
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        var barcode_table = $('#barcode_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader:false,
            buttons:[],
            ajax: '/barcodes',
            bPaginate: false,
            columnDefs: [ {
                "targets": 2,
                "orderable": false,
                "searchable": false
            } ]
        });
        $(document).on('click', 'button.delete_barcode_button', function(){
            var is_confirmed = confirm("{{ __('barcode.delete_confirm') }}");
            if(!is_confirmed){
                return;
            }

            var href = $(this).data('href');
            var data = $(this).serialize();

            $.ajax({
                method: "DELETE",
                url: href,
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success === true){
                        toastr.success(result.msg);
                        barcode_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
        $(document).on('click', 'button.set_default', function(){
            var href = $(this).data('href');
            var data = $(this).serialize();

            $.ajax({
                method: "get",
                url: href,
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success === true){
                        toastr.success(result.msg);
                        barcode_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    });
</script>

</div>{{-- .page-modern --}}
@endsection