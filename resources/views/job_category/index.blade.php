@extends('layouts.app')
@section('title', __('job.category'))


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
                    <i class="fas fa-folder"></i>
                </div>
                <div>
                    <h1>@lang('job.category')</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('job.category')])
        @can('job.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" 
                        data-href="{{action([\App\Http\Controllers\JobCategoryController::class, 'create'])}}" 
                        data-container=".job_category_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )
                    </button>
                </div>
            @endslot
        @endcan
        @can('job.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="job_category_table">
                    <thead>
                        <tr>
                            <th>@lang('job.category')</th>
                            <th>@lang('lang_v1.short_code')</th>
                            <th>@lang('job.description')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade job_category_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        job_category_table = $('#job_category_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{action([\App\Http\Controllers\JobCategoryController::class, "index"])}}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'short_code', name: 'short_code' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('submit', 'form#job_category_add_form', function(e) {
            e.preventDefault();
            var data = $(this).serialize();

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success === true) {
                        $('div.job_category_modal').modal('hide');
                        toastr.success(result.msg);
                        job_category_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });

        $(document).on('click', 'button.edit_category_button', function() {
            $('div.job_category_modal').load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#job_category_edit_form').submit(function(e) {
                    e.preventDefault();
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'POST',
                        url: $(this).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success === true) {
                                $('div.job_category_modal').modal('hide');
                                toastr.success(result.msg);
                                job_category_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                });
            });
        });

        $(document).on('click', 'button.delete_category_button', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var url = $(this).data('href');
                    var data = { _method: 'DELETE' };

                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success === true) {
                                toastr.success(result.msg);
                                job_category_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
    });
</script>

</div>{{-- .page-modern --}}
@endsection