<div class="box box-default" style="border-left: 4px solid #3c8dbc; margin-bottom: 12px;">
    <div class="box-header with-border">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
            <div>
                <h4 style="margin:0; font-size:15px;">{{ $approval->title }}</h4>
                <small class="text-muted">
                    <span class="label label-default">{{ class_basename($approval->approvable_type) }}</span>
                    &nbsp; Submitted by <strong>{{ $approval->requester->first_name ?? '' }} {{ $approval->requester->last_name ?? '' }}</strong>
                    &nbsp; · &nbsp; {{ $approval->created_at->diffForHumans() }}
                </small>
            </div>
            <div>
                @if($approval->flow)
                    Step <strong>{{ $approval->current_step }}</strong> of <strong>{{ $approval->totalSteps() }}</strong>
                    <div class="progress" style="width:120px; margin:4px 0 0; height:6px;">
                        <div class="progress-bar progress-bar-info" style="width:{{ $approval->progressPercent() }}%"></div>
                    </div>
                @else
                    <span class="label label-warning">No Flow Assigned</span>
                @endif
            </div>
        </div>
    </div>

    <div class="box-body" style="padding:12px 15px;">
        {{-- Audit trail --}}
        @if($approval->decisions->count())
        <div style="margin-bottom:12px;">
            @foreach($approval->decisions as $d)
            <div style="display:flex; gap:8px; margin-bottom:6px; font-size:13px;">
                <span class="label {{ $d->decision === 'approved' ? 'label-success' : ($d->decision === 'rejected' ? 'label-danger' : 'label-warning') }}">
                    Step {{ $d->step_number }}: {{ ucfirst($d->decision) }}
                </span>
                <span class="text-muted">by <strong>{{ $d->decider->first_name ?? '' }}</strong>
                    @if($d->comment) — "{{ $d->comment }}" @endif
                    · {{ \Carbon\Carbon::parse($d->decided_at)->format('d M H:i') }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Action buttons --}}
        @if($show_actions)
        <form method="POST" action="{{ route('approvals.decide', $approval) }}" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
            @csrf
            <div style="flex:1; min-width:200px;">
                <input type="text" name="comment" class="form-control input-sm" placeholder="Optional comment...">
            </div>
            <button type="submit" name="decision" value="approved" class="btn btn-sm btn-success">
                <i class="fa fa-check"></i> Approve
            </button>
            <button type="submit" name="decision" value="returned" class="btn btn-sm btn-warning"
                onclick="return confirm('Return this request for revision?')">
                <i class="fa fa-undo"></i> Return
            </button>
            <button type="submit" name="decision" value="rejected" class="btn btn-sm btn-danger"
                onclick="return confirm('Reject this request? This cannot be undone.')">
                <i class="fa fa-times"></i> Reject
            </button>
        </form>
        @elseif($approval->status === 'returned' && $approval->requested_by === auth()->id())
        <form method="POST" action="{{ route('approvals.resubmit', $approval) }}" style="display:flex; gap:8px; align-items:flex-end;">
            @csrf
            <div style="flex:1;">
                <input type="text" name="notes" class="form-control input-sm" placeholder="Add a note before resubmitting...">
            </div>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-paper-plane"></i> Resubmit</button>
        </form>
        @endif
    </div>
</div>
