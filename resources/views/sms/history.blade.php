@extends('layouts.app')
@section('title', 'SMS History')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">SMS History</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'SMS Send History'])
        <div class="row" style="margin-bottom:12px">
            <div class="col-md-12">
                <a href="{{ route('sms.send') }}" class="btn btn-primary">
                    <i class="fa fa-paper-plane"></i> Send New SMS
                </a>
            </div>
        </div>

        @if(is_null($logs))
            <div class="alert alert-warning">
                SMS history table not found. Please run <code>php artisan migrate</code> to set it up.
            </div>
        @elseif($logs->total() === 0)
            <div class="alert alert-info">No SMS have been sent yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date / Time</th>
                            <th>Sent By</th>
                            <th>Recipients</th>
                            <th>Sent</th>
                            <th>Failed</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
                            <td>{{ optional(\App\User::find($log->sent_by))->first_name ?? 'System' }}</td>
                            <td>
                                @switch($log->recipient_type)
                                    @case('all_customers') All Customers @break
                                    @case('all_suppliers') All Suppliers @break
                                    @case('group') Customer Group @break
                                    @default Manual @break
                                @endswitch
                            </td>
                            <td><span class="label label-success">{{ $log->total_sent }}</span></td>
                            <td>
                                @if($log->total_failed > 0)
                                    <span class="label label-danger">{{ $log->total_failed }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                <span title="{{ $log->message }}">
                                    {{ \Illuminate\Support\Str::limit($log->message, 60) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $logs->links() }}
        @endif
    @endcomponent
</section>
@endsection
