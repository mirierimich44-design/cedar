@extends('layouts.app')
@section('title', __('job.job_card') . ' Templates')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('job.job_card') Templates</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('job.job_card') . ' Templates'])
        @can('job.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right" 
                        href="{{action([\App\Http\Controllers\JobTemplateController::class, 'create'])}}">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )
                    </a>
                </div>
            @endslot
        @endcan
        @can('job.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="job_template_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('job.title')</th>
                            <th>@lang('job.category')</th>
                            <th>@lang('job.estimated_hours')</th>
                            <th>@lang('job.estimated_cost')</th>
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
        job_template_table = $('#job_template_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{action([\App\Http\Controllers\JobTemplateController::class, "index"])}}',
            columnDefs: [ {
                "targets": 0,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'action', name: 'action' },
                { data: 'name', name: 'job_templates.name' },
                { data: 'category_name', name: 'cat.name' },
                { data: 'estimated_hours', name: 'job_templates.estimated_hours' },
                { data: 'estimated_cost', name: 'job_templates.estimated_cost' }
            ]
        });

        $(document).on('click', 'button.delete_template_button', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var url = $(this).data('href');
                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success === true) {
                                toastr.success(result.msg);
                                job_template_table.ajax.reload();
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
@endsection
