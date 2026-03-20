@extends('layouts.app')
@section('title', __('job.add'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('job.add')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\JobController::class, 'store']), 'method' => 'post', 'id' => 'job_add_form' ]) !!}
    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('location_id', __('job.location') . ':*') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('contact_id', __('job.contact') . ':*') !!}
                    {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('category_id', __('job.category') . ':') !!}
                    {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('title', __('job.title') . ':*') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'required', 'placeholder' => __('job.title')]); !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('template_id', __('job.job_card') . ' Template:') !!}
                    {!! Form::select('template_id', $templates, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
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
                    {!! Form::label('priority', __('job.priority') . ':*') !!}
                    {!! Form::select('priority', $priorities, 'medium', ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('due_date', __('job.due_date') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('due_date', null, ['class' => 'form-control', 'id' => 'due_date', 'readonly']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('assigned_to', __('job.assigned_to') . ':') !!}
                    {!! Form::select('assigned_to', $users, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
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
                            {!! Form::checkbox('requires_approval', 1, false, ['class' => 'input-icheck']); !!} @lang('job.requires_approval')
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
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
        $('#due_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
    });
</script>
@endsection
