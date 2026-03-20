@extends('layouts.app')
@section('title', __('job.view'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('job.view') - {{$job->ref_no}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-4">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('job.job_card') . ' Details'])
                <table class="table table-bordered">
                    <tr>
                        <th>@lang('job.ref_no')</th>
                        <td>{{$job->ref_no}}</td>
                    </tr>
                    <tr>
                        <th>@lang('job.title')</th>
                        <td>{{$job->title}}</td>
                    </tr>
                    <tr>
                        <th>@lang('job.location')</th>
                        <td>{{$job->location->name ?? ''}}</td>
                    </tr>
                    <tr>
                        <th>@lang('job.contact')</th>
                        <td>{{$job->contact->name ?? ''}}</td>
                    </tr>
                    <tr>
                        <th>@lang('job.category')</th>
                        <td>{{$job->category->name ?? ''}}</td>
                    </tr>
                    <tr>
                        <th>@lang('job.status')</th>
                        <td>@lang('job.' . $job->status)</td>
                    </tr>
                    <tr>
                        <th>@lang('job.priority')</th>
                        <td>@lang('job.' . $job->priority)</td>
                    </tr>
                    <tr>
                        <th>@lang('job.due_date')</th>
                        <td>{{!empty($job->due_date) ? @format_datetime($job->due_date) : ''}}</td>
                    </tr>
                    <tr>
                        <th>@lang('job.assigned_to')</th>
                        <td>{{$job->assignedUser->user_full_name ?? ''}}</td>
                    </tr>
                </table>
            @endcomponent
        </div>

        <div class="col-md-8">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#checklists_tab" data-toggle="tab">@lang('job.checklists')</a></li>
                    <li><a href="#logs_tab" data-toggle="tab">@lang('job.logs')</a></li>
                    <li><a href="#images_tab" data-toggle="tab">@lang('job.images')</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="checklists_tab">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('job.title')</th>
                                    <th>@lang('job.description')</th>
                                    <th>@lang('job.status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($job->checklists as $checklist)
                                    <tr>
                                        <td>{{$checklist->title}}</td>
                                        <td>{{$checklist->description}}</td>
                                        <td>
                                            <input type="checkbox" class="checklist_toggle" 
                                                data-href="{{action([\App\Http\Controllers\JobController::class, 'toggleChecklist'], [$checklist->id])}}" 
                                                {{$checklist->is_completed ? 'checked' : ''}}>
                                            @if($checklist->is_completed)
                                                <span class="label bg-green">@lang('job.completed')</span>
                                            @else
                                                <span class="label bg-yellow">@lang('job.pending')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="logs_tab">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('job.status')</th>
                                    <th>@lang('brand.note')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($job->logs as $log)
                                    <tr>
                                        <td>{{@format_datetime($log->created_at)}}</td>
                                        <td>@lang('job.' . $log->status)</td>
                                        <td>{{$log->note}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="images_tab">
                        <div class="row">
                            @foreach($job->images as $image)
                                <div class="col-md-3">
                                    <img src="{{$image->image_url}}" class="img-responsive img-thumbnail">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('change', '.checklist_toggle', function() {
            var href = $(this).data('href');
            var is_checked = $(this).is(':checked');
            var self = $(this);

            $.ajax({
                method: "POST",
                url: href,
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        location.reload();
                    } else {
                        toastr.error(result.msg);
                        self.prop('checked', !is_checked);
                    }
                }
            });
        });
    });
</script>
@endsection
