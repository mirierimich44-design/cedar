@extends('layouts.app')
@section('title', __('lang_v1.notification_templates'))


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
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h1>@lang('lang_v1.notification_templates')</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>
<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\NotificationTemplateController::class, 'store']), 'method' => 'post' ]) !!}

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.notifications') . ':'])
                @include('notification_template.partials.tabs', ['templates' => $general_notifications])
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.customer_notifications') . ':'])
                @include('notification_template.partials.tabs', ['templates' => $customer_notifications])
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.supplier_notifications') . ':'])
                @include('notification_template.partials.tabs', ['templates' => $supplier_notifications])

                <div class="callout callout-warning">
                    <p>@lang('lang_v1.logo_not_work_in_sms'):</p>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" class="tw-dw-btn tw-dw-btn-error tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
        </div>
    </div>
    {!! Form::close() !!}

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $('textarea.ckeditor').each( function(){
        var editor_id = $(this).attr('id');
        tinymce.init({
            selector: 'textarea#'+editor_id,
        });
    });
</script>

</div>{{-- .page-modern --}}
@endsection