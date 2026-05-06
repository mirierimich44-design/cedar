@extends('layouts.app')
@section('title', __('job.jobs'))


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
                    <i class="fas fa-wrench"></i>
                </div>
                <div>
                    <h1>@lang('job.jobs')</h1>
                    <p class="pg-subtitle">@lang('job.manage_your_jobs') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('job_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('job_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('job_list_filter_contact_id',  __('contact.customer') . ':') !!}
                {!! Form::select('job_list_filter_contact_id', $contacts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('job_list_filter_status',  __('sale.status') . ':') !!}
                {!! Form::select('job_list_filter_status', $statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('job_list_filter_priority',  __('job.priority') . ':') !!}
                {!! Form::select('job_list_filter_priority', $priorities, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('job.all_your_jobs')])
        @can('job.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                        href="{{action([\App\Http\Controllers\JobController::class, 'create'])}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                </div>
            @endslot
        @endcan
        @can('job.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="jobs_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('job.ref_no')</th>
                            <th>@lang('job.title')</th>
                            <th>@lang('job.location')</th>
                            <th>@lang('job.contact')</th>
                            <th>@lang('job.assigned_to')</th>
                            <th>@lang('job.priority')</th>
                            <th>@lang('job.status')</th>
                            <th>@lang('job.due_date')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        jobs_table = $('#jobs_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            ajax: {
                url: '{{action([\App\Http\Controllers\JobController::class, "index"])}}',
                data: function(d) {
                    d.location_id = $('#job_list_filter_location_id').val();
                    d.contact_id = $('#job_list_filter_contact_id').val();
                    d.status = $('#job_list_filter_status').val();
                    d.priority = $('#job_list_filter_priority').val();
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false},
                { data: 'ref_no', name: 'job_cards.ref_no' },
                { data: 'title', name: 'job_cards.title' },
                { data: 'location_name', name: 'bl.name' },
                { data: 'contact_name', name: 'c.name' },
                { data: 'assigned_to_name', name: 'assigned_to_name', searchable: false },
                { data: 'priority', name: 'job_cards.priority' },
                { data: 'status', name: 'job_cards.status' },
                { data: 'due_date', name: 'job_cards.due_date' }
            ]
        });

        $(document).on('change', '#job_list_filter_location_id, #job_list_filter_contact_id, #job_list_filter_status, #job_list_filter_priority', function() {
            jobs_table.ajax.reload();
        });

        $(document).on('click', 'a.delete_job_button', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_product,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                jobs_table.ajax.reload();
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