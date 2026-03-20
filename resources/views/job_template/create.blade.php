@extends('layouts.app')
@section('title', __('job.job_card') . ' Template')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('job.job_card') Template</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\JobTemplateController::class, 'store']), 'method' => 'post', 'id' => 'job_template_add_form' ]) !!}
    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('name', __('job.title') . ':*') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('job.title')]); !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('category_id', __('job.category') . ':') !!}
                    {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('description', __('job.description') . ':') !!}
                    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('estimated_hours', __('job.estimated_hours') . ':') !!}
                    {!! Form::number('estimated_hours', null, ['class' => 'form-control', 'step' => '0.01']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('estimated_cost', __('job.estimated_cost') . ':') !!}
                    {!! Form::number('estimated_cost', null, ['class' => 'form-control', 'step' => '0.01']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" style="margin-top: 25px;">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('requires_approval', 1, true, ['class' => 'input-icheck']); !!} @lang('job.requires_approval')
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('job.checklists')])
        <div class="table-responsive">
            <table class="table table-bordered" id="checklist_table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>@lang('job.title')</th>
                        <th>@lang('job.description')</th>
                        <th class="text-center"><i class="fa fa-trash"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>{!! Form::text('items[0][title]', null, ['class' => 'form-control', 'required']); !!}</td>
                        <td>{!! Form::text('items[0][description]', null, ['class' => 'form-control']); !!}</td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-xs remove_row"><i class="fa fa-times"></i></button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary btn-xs add_row"><i class="fa fa-plus"></i> @lang('messages.add')</button>
        </div>
        <div class="row">
            <div class="col-md-12 text-center" style="margin-top: 20px;">
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
            </div>
        </div>
    @endcomponent
    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var row_index = 1;
        $(document).on('click', '.add_row', function() {
            var html = '<tr>' +
                '<td class="text-center">' + (row_index + 1) + '</td>' +
                '<td><input class="form-control" required name="items[' + row_index + '][title]" type="text"></td>' +
                '<td><input class="form-control" name="items[' + row_index + '][description]" type="text"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-danger btn-xs remove_row"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
            $('#checklist_table tbody').append(html);
            row_index++;
        });

        $(document).on('click', '.remove_row', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection
