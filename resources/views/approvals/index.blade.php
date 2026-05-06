@extends('layouts.app')
@section('title', 'Approvals')


@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')

<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h1>Approvals</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_mine" data-toggle="tab">
                        <i class="fa fa-bell text-red"></i> Needs My Action
                        @if($pending_mine->count()) <span class="badge bg-red">{{ $pending_mine->count() }}</span> @endif
                    </a></li>
                    <li><a href="#tab_all" data-toggle="tab"><i class="fa fa-clock-o"></i> All Pending</a></li>
                    <li><a href="#tab_history" data-toggle="tab"><i class="fa fa-history"></i> History</a></li>
                    <li class="pull-right">
                        <a href="{{ route('approvals.flows') }}" class="btn btn-sm btn-default" style="margin-top:5px;margin-right:10px;">
                            <i class="fa fa-cog"></i> Manage Flows
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- NEEDS MY ACTION --}}
                    <div class="tab-pane active" id="tab_mine">
                        @if($pending_mine->isEmpty())
                            <div class="text-center" style="padding:40px;">
                                <i class="fa fa-check-circle fa-3x text-green"></i>
                                <p class="text-muted" style="margin-top:10px;">Nothing waiting for your action. You're all caught up!</p>
                            </div>
                        @else
                            @foreach($pending_mine as $approval)
                                @include('approvals._card', ['approval' => $approval, 'show_actions' => true])
                            @endforeach
                        @endif
                    </div>

                    {{-- ALL PENDING --}}
                    <div class="tab-pane" id="tab_all">
                        @forelse($all_pending as $approval)
                            @include('approvals._card', ['approval' => $approval, 'show_actions' => false])
                        @empty
                            <div class="text-center" style="padding:40px;"><p class="text-muted">No pending approvals.</p></div>
                        @endforelse
                    </div>

                    {{-- HISTORY --}}
                    <div class="tab-pane" id="tab_history">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Submitted By</th>
                                        <th>Status</th>
                                        <th>Last Action</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($history as $approval)
                                    <tr>
                                        <td>{{ $approval->title }}</td>
                                        <td><span class="label label-default">{{ class_basename($approval->approvable_type) }}</span></td>
                                        <td>{{ $approval->requester->first_name ?? 'N/A' }} {{ $approval->requester->last_name ?? '' }}</td>
                                        <td>
                                            @if($approval->status === 'approved')
                                                <span class="label label-success">Approved</span>
                                            @elseif($approval->status === 'rejected')
                                                <span class="label label-danger">Rejected</span>
                                            @else
                                                <span class="label label-warning">Returned</span>
                                            @endif
                                        </td>
                                        <td>{{ $approval->decisions->last()?->comment ?? '—' }}</td>
                                        <td>{{ $approval->updated_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted">No history yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>{{-- .page-modern --}}
@endsection