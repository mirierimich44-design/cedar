{{-- Shared date/location filter for advanced reports --}}
@php
    $showMethod = $showMethod ?? false;
    $showMonths = $showMonths ?? false;
    $extra = $extra ?? '';
@endphp
<form method="get" class="no-print" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;align-items:flex-end">
    @if(!empty($showMonths))
        <div>
            <label style="font-size:12px;font-weight:700;display:block">Months</label>
            <select name="months" class="form-control" style="min-width:100px">
                @foreach([3,6,9,12] as $m)
                    <option value="{{ $m }}" @if((int)($months ?? 6) === $m) selected @endif>{{ $m }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div>
            <label style="font-size:12px;font-weight:700;display:block">From</label>
            <input type="date" name="start_date" value="{{ $start ?? '' }}" class="form-control" style="min-width:150px">
        </div>
        <div>
            <label style="font-size:12px;font-weight:700;display:block">To</label>
            <input type="date" name="end_date" value="{{ $end ?? '' }}" class="form-control" style="min-width:150px">
        </div>
    @endif
    @if(isset($business_locations))
    <div>
        <label style="font-size:12px;font-weight:700;display:block">Location</label>
        <select name="location_id" class="form-control select2" style="min-width:180px">
            @if(!empty($allow_all_locations))
                <option value="">All locations</option>
            @endif
            @foreach($business_locations as $id => $name)
                @if((string)$id !== '' || !empty($allow_all_locations))
                <option value="{{ $id }}" @if((string)($location_id ?? '') === (string)$id) selected @endif>{{ $name }}</option>
                @endif
            @endforeach
        </select>
    </div>
    @endif
    @if(!empty($showMethod))
    <div>
        <label style="font-size:12px;font-weight:700;display:block">Payment method</label>
        <select name="method" class="form-control" style="min-width:140px">
            @foreach(['all'=>'All','cash'=>'Cash','card'=>'Card','cheque'=>'Cheque','bank_transfer'=>'Bank','mpesa'=>'M-Pesa / mobile','custom_pay_1'=>'Custom 1','custom_pay_2'=>'Custom 2','custom_pay_3'=>'Custom 3'] as $k=>$lab)
                <option value="{{ $k }}" @if(($method ?? 'all') === $k) selected @endif>{{ $lab }}</option>
            @endforeach
        </select>
    </div>
    @endif
    {!! $extra !!}
    <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Load</button>
    <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
</form>
