@extends('layouts.app')
@section('title', 'Approval Flows')

@section('content')
<section class="content-header">
    <h1>Approval Flows <small>Configure multi-step approval workflows</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('approvals.index') }}">Approvals</a></li>
        <li class="active">Flows</li>
    </ol>
</section>

<section class="content">
    <div class="row">

        {{-- Create Flow Form --}}
        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-plus"></i> Create New Flow</h3>
                </div>
                <div class="box-body">
                    <form method="POST" action="{{ route('approvals.flows.store') }}" id="flow-form">
                        @csrf
                        <div class="form-group">
                            <label>Flow Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Purchase Order > KES 50,000" required>
                        </div>
                        <div class="form-group">
                            <label>Applies To</label>
                            <select name="approvable_type" class="form-control" required>
                                <option value="">-- Select Document Type --</option>
                                <option value="App\PurchaseOrder">Purchase Order</option>
                                <option value="App\Transaction">Sale / Invoice</option>
                                <option value="App\Expense">Expense</option>
                                <option value="App\Hospital\Admission">Hospital Admission</option>
                                <option value="App\Hospital\Surgery">Theatre / Surgery</option>
                                <option value="custom">Other (custom)</option>
                            </select>
                        </div>

                        <label>Approval Steps <small class="text-muted">(in order)</small></label>
                        <div id="steps-container">
                            <div class="step-row well well-sm" style="margin-bottom:8px;">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <label style="font-size:12px;">Label</label>
                                        <input type="text" name="steps[0][label]" class="form-control input-sm" placeholder="e.g. HOD">
                                    </div>
                                    <div class="col-xs-5">
                                        <label style="font-size:12px;">Role</label>
                                        <select name="steps[0][role_id]" class="form-control input-sm">
                                            <option value="">-- Any Role --</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xs-2" style="padding-top:19px;">
                                        <button type="button" class="btn btn-sm btn-danger remove-step"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-step" class="btn btn-sm btn-default" style="margin-bottom:15px;">
                            <i class="fa fa-plus"></i> Add Step
                        </button>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save Flow</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Existing Flows --}}
        <div class="col-md-7">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list"></i> Existing Flows</h3>
                </div>
                <div class="box-body">
                    @forelse($flows as $flow)
                    <div class="panel panel-default">
                        <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;">
                            <strong>{{ $flow->name }}</strong>
                            <div>
                                <span class="label label-info">{{ class_basename($flow->approvable_type) }}</span>
                                <span class="label {{ $flow->is_active ? 'label-success' : 'label-default' }}">
                                    {{ $flow->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <form method="POST" action="{{ route('approvals.flows.destroy', $flow) }}" style="display:inline;" onsubmit="return confirm('Delete this flow?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" style="margin-left:6px;"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="panel-body" style="padding:10px 15px;">
                            <ol style="margin:0; padding-left:20px;">
                                @foreach($flow->steps as $step)
                                <li style="font-size:13px; margin-bottom:4px;">
                                    <strong>{{ $step->label ?: 'Step ' . $step->order }}</strong>
                                    @if($step->assignedRole) — Role: <em>{{ $step->assignedRole->name }}</em> @endif
                                    @if($step->assignedUser) — User: <em>{{ $step->assignedUser->first_name }}</em> @endif
                                </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                    @empty
                        <p class="text-muted text-center">No flows configured yet. Create one to get started.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@section('javascript')
<script>
var stepCount = 1;
$('#add-step').click(function() {
    var i = stepCount++;
    $('#steps-container').append(`
        <div class="step-row well well-sm" style="margin-bottom:8px;">
            <div class="row">
                <div class="col-xs-5">
                    <label style="font-size:12px;">Label</label>
                    <input type="text" name="steps[${i}][label]" class="form-control input-sm" placeholder="e.g. Finance">
                </div>
                <div class="col-xs-5">
                    <label style="font-size:12px;">Role</label>
                    <select name="steps[${i}][role_id]" class="form-control input-sm">
                        <option value="">-- Any Role --</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-2" style="padding-top:19px;">
                    <button type="button" class="btn btn-sm btn-danger remove-step"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>`);
});
$(document).on('click', '.remove-step', function() {
    if ($('.step-row').length > 1) $(this).closest('.step-row').remove();
});
</script>
@endsection
