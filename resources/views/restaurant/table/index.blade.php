@extends('layouts.app')
@section('title', __('restaurant.tables'))


@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')

    <!-- Content Header (Page header) -->
    
<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-chair"></i>
                </div>
                <div>
                    <h1>@lang('restaurant.tables')</h1>
                    <p class="pg-subtitle">@lang('restaurant.manage_your_tables') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<!-- Main content -->
    <section class="content">

        {{-- <div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'restaurant.all_your_tables' )</h3>
            @can('restaurant.create')
            	<div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    	data-href="{{action([\App\Http\Controllers\Restaurant\TableController::class, 'create'])}}" 
                    	data-container=".tables_modal">
                    	<i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('restaurant.view')
            	<table class="table table-bordered table-striped" id="tables_table">
            		<thead>
            			<tr>
            				<th>@lang( 'restaurant.table' )</th>
                            <th>@lang( 'purchase.business_location' )</th>
            				<th>@lang( 'restaurant.description' )</th>
            				<th>@lang( 'messages.action' )</th>
            			</tr>
            		</thead>
            	</table>
            @endcan
        </div>
    </div> --}}

        @component('components.widget')
            <div class="box-header">
                <h3 class="box-title">@lang('restaurant.all_your_tables')</h3>
                @can('restaurant.create')
                    <div class="box-tools">
                        {{-- <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action([\App\Http\Controllers\Restaurant\TableController::class, 'create']) }}"
                            data-container=".tables_modal">
                            <i class="fa fa-plus"></i> @lang('messages.add')</button> --}}
                        <button class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal"
                            data-href="{{ action([\App\Http\Controllers\Restaurant\TableController::class, 'create']) }}"
                            data-container=".tables_modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                        </button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('restaurant.view')
                    <table class="table table-bordered table-striped" id="tables_table">
                        <thead>
                            <tr>
                                <th>@lang('restaurant.table')</th>
                                <th>@lang('purchase.business_location')</th>
                                <th>@lang('restaurant.description')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                @endcan
            </div>
        @endcomponent

        <div class="modal fade tables_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on('submit', 'form#table_add_form', function(e) {
                e.preventDefault();
                var data = $(this).serialize();

                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div.tables_modal').modal('hide');
                            toastr.success(result.msg);
                            tables_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            //Brands table
            var tables_table = $('#tables_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                ajax: '/modules/tables',
                columnDefs: [{
                    "targets": 3,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'name',
                        name: 'res_tables.name'
                    },
                    {
                        data: 'location',
                        name: 'BL.name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
            });

            $(document).on('click', 'button.edit_table_button', function() {

                $("div.tables_modal").load($(this).data('href'), function() {

                    $(this).modal('show');

                    $('form#table_edit_form').submit(function(e) {
                        e.preventDefault();
                        var data = $(this).serialize();

                        $.ajax({
                            method: "POST",
                            url: $(this).attr("action"),
                            dataType: "json",
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    $('div.tables_modal').modal('hide');
                                    toastr.success(result.msg);
                                    tables_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    });
                });
            });

            $(document).on('click', 'button.delete_table_button', function() {
                swal({
                    title: LANG.sure,
                    text: LANG.confirm_delete_table,
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
                                    tables_table.ajax.reload();
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

</div>{{-- .page-modern --}}
@endsection