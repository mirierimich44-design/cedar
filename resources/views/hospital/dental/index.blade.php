@extends('layouts.app')
@section('title', 'Dental Charting')

@section('content')
<section class="content-header">
    <h1>Dental Consultation - {{ $patient->name }}</h1>
</section>

<section class="content">
    <div class="row">
        {{-- Tooth Map --}}
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Adult Tooth Map (Permanent)</h3>
                </div>
                <div class="box-body">
                    <div class="tooth-map" style="display: flex; flex-direction: column; gap: 20px; align-items: center; background: #f8fafc; padding: 20px; border-radius: 10px;">
                        
                        {{-- Upper Row --}}
                        <div class="upper-row" style="display: flex; gap: 10px;">
                            @foreach(range(1, 16) as $i)
                                @php $t = $teeth[$i] ?? null; @endphp
                                <div class="tooth-item text-center" style="cursor: pointer;" onclick="markTooth({{ $i }})">
                                    <div id="tooth-{{ $i }}" class="tooth-icon" style="width: 35px; height: 45px; border: 2px solid #cbd5e1; border-radius: 5px; background: {{ $t && $t->status ? '#fbbf24' : 'white' }}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                        {{ $i }}
                                    </div>
                                    <small style="font-size: 10px; color: #64748b;">{{ $t->status ?? '' }}</small>
                                </div>
                            @endforeach
                        </div>

                        <div style="width: 100%; height: 2px; background: #e2e8f0;"></div>

                        {{-- Lower Row --}}
                        <div class="lower-row" style="display: flex; gap: 10px;">
                            @foreach(range(17, 32) as $i)
                                @php $t = $teeth[$i] ?? null; @endphp
                                <div class="tooth-item text-center" style="cursor: pointer;" onclick="markTooth({{ $i }})">
                                    <div id="tooth-{{ $i }}" class="tooth-icon" style="width: 35px; height: 45px; border: 2px solid #cbd5e1; border-radius: 5px; background: {{ $t && $t->status ? '#fbbf24' : 'white' }}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                        {{ $i }}
                                    </div>
                                    <small style="font-size: 10px; color: #64748b;">{{ $t->status ?? '' }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <h4>Legend:</h4>
                        <span class="label bg-white" style="border: 1px solid #ccc; color: #333;">Healthy</span>
                        <span class="label bg-yellow">Decayed/Issues</span>
                        <span class="label bg-red">Missing</span>
                    </div>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Procedure History</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Procedure</th>
                                <th>Doctor</th>
                                <th>Status</th>
                                <th>Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($procedures as $proc)
                                <tr>
                                    <td>{{ $proc->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $proc->procedure_name }}</td>
                                    <td>{{ $proc->doctor->first_name }}</td>
                                    <td><span class="label bg-green">{{ ucfirst($proc->status) }}</span></td>
                                    <td>{{ @num_format($proc->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Add Procedure Form --}}
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">Record Procedure</h3>
                </div>
                {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\DentalController::class, 'addProcedure']), 'method' => 'post']) !!}
                {!! Form::hidden('patient_id', $patient->id) !!}
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('procedure_name', 'Procedure:*') !!}
                        {!! Form::select('procedure_name', [
                            'Extraction' => 'Extraction',
                            'Filling' => 'Filling',
                            'Root Canal' => 'Root Canal',
                            'Scaling & Polishing' => 'Scaling & Polishing',
                            'Braces Adjustment' => 'Braces Adjustment',
                            'Crown Placement' => 'Crown Placement'
                        ], null, ['class' => 'form-control', 'required', 'placeholder' => 'Select Procedure']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('price', 'Procedure Fee:*') !!}
                        {!! Form::number('price', 0, ['class' => 'form-control', 'required', 'step' => '0.01']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('notes', 'Additional Notes:') !!}
                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-block">Add to Bill & Complete</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

{{-- Tooth Status Modal --}}
<div class="modal fade" id="toothModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tooth #<span id="display-tooth-num"></span> Status</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-tooth-num">
                <div class="form-group">
                    <label>Condition:</label>
                    <select id="tooth-status" class="form-control">
                        <option value="">Healthy</option>
                        <option value="decayed">Decayed</option>
                        <option value="missing">Missing</option>
                        <option value="filled">Filled</option>
                        <option value="crown">Crown</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes:</label>
                    <textarea id="tooth-notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveToothStatus()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
    function markTooth(num) {
        $('#display-tooth-num').text(num);
        $('#modal-tooth-num').val(num);
        $('#toothModal').modal('show');
    }

    function saveToothStatus() {
        var num = $('#modal-tooth-num').val();
        var status = $('#tooth-status').val();
        var notes = $('#tooth-notes').val();

        $.post('{{ action([\App\Http\Controllers\Hospital\DentalController::class, "updateTooth"]) }}', {
            _token: '{{ csrf_token() }}',
            patient_id: '{{ $patient->id }}',
            tooth_number: num,
            status: status,
            notes: notes
        }, function(res) {
            if(res.success) {
                location.reload();
            }
        });
    }
</script>
@endsection
