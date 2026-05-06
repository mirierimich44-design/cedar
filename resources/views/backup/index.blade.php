@extends('layouts.app')
@section('title', __('lang_v1.backup'))


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
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h1>@lang('lang_v1.backup')</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<!-- Main content -->
<section class="content">
    
  @if (session('notification') || !empty($notification))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @if(!empty($notification['msg']))
                    {{$notification['msg']}}
                @elseif(session('notification.msg'))
                    {{ session('notification.msg') }}
                @endif
              </div>
          </div>  
      </div>     
  @endif

  <div class="row">
    <div class="col-sm-12">
      @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
          <div class="box-tools">
            <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal-coupon"
                    href="{{ url('backup/create') }}" id="create-new-backup-button" style="margin-bottom:2em;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg> @lang('messages.add')
              </a>
          </div>
        @endslot
        @if (count($backups))
                <table class="table table-striped table-bordered">
                  <thead>
                  <tr>
                      <th>@lang('lang_v1.file')</th>
                      <th>@lang('lang_v1.size')</th>
                      <th>@lang('lang_v1.date')</th>
                      <th>@lang('lang_v1.age')</th>
                      <th>@lang('messages.actions')</th>
                  </tr>
                  </thead>
                    <tbody>
                    @foreach($backups as $backup)
                        <tr>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ humanFilesize($backup['file_size']) }}</td>
                            <td>
                                {{ Carbon::createFromTimestamp($backup['last_modified'])->toDateTimeString() }}
                            </td>
                            <td>
                                {{ Carbon::createFromTimestamp($backup['last_modified'])->diffForHumans(Carbon::now()) }}
                            </td>
                            <td>
                              <a class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent"
                                   href="{{action([\App\Http\Controllers\BackUpController::class, 'download'], [$backup['file_name']])}}"><i
                                        class="fas fa-cloud-download-alt"></i> @lang('lang_v1.download')</a>
                                <a class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error link_confirmation" data-button-type="delete"
                                   href="{{ route('delete_backup', $backup['file_name']) }}"><i class="fas fa-trash-alt"></i>
                                    @lang('messages.delete') </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
              </table>
            @else
                <div class="well">
                    <h4>There are no backups</h4>
                </div>
            @endif
            <br>
            <strong>@lang('lang_v1.auto_backup_instruction'):</strong><br>
            <code>{{$cron_job_command}}</code> <br>
      @endcomponent
    </div>
  </div>
</section>

</div>{{-- .page-modern --}}
@endsection