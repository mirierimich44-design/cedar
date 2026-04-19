@extends('layouts.app')
@section('title', 'Dispense Medication')

@section('content')
<section class="content-header">
    <h1>Dispense Medication - {{ $patient->name }}</h1>
</section>

<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\PharmacyController::class, 'storeDispense']), 'method' => 'post']) !!}
    {!! Form::hidden('patient_id', $patient->id) !!}
    
    <div class="box box-primary">
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="bg-gray">
                        <th style="width: 50px;">Select</th>
                        <th>Medicine</th>
                        <th>Dosage / Freq</th>
                        <th>Duration</th>
                        <th style="width: 100px;">Prescribed Qty</th>
                        <th style="width: 120px;">Dispense Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $p)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="items[{{ $p->id }}][dispense]" value="1" checked>
                            </td>
                            <td>
                                <strong>{{ $p->variation->product->name ?? $p->drug_name }}</strong>
                                @if($p->variation)
                                    <br><small class="text-muted">SKU: {{ $p->variation->sub_sku }}</small>
                                @endif
                            </td>
                            <td>{{ $p->dosage }} ({{ $p->frequency }})</td>
                            <td>{{ $p->duration }}</td>
                            <td>{{ @format_quantity($p->quantity) }}</td>
                            <td>
                                <input type="number" name="items[{{ $p->id }}][quantity]" class="form-control input-sm" value="{{ $p->quantity }}" step="any">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-success btn-lg">Complete Dispensing & Deduct Stock</button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection
