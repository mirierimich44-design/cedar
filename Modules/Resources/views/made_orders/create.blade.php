@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('made_to_order::messages.create_order') }}</h2>

    @if($errors->any()) <div class="alert alert-danger">{{ implode(', ', $errors->all()) }}</div> @endif

    <form method="post" action="{{ route('made_to_order.store') }}">
        @csrf
        <div class="mb-3">
            <label>{{ __('made_to_order::messages.customer_id') }}</label>
            <input type="text" name="customer_id" class="form-control" />
        </div>

        <h5>{{ __('made_to_order::messages.items') }}</h5>
        <div id="items-area">
            <div class="item-row mb-3 border p-3 rounded">
                <input type="text" name="items[0][item_name]" placeholder="{{ __('made_to_order::messages.product_name') }}" class="form-control mb-1" />
                <div class="row">
                    <div class="col-md-3"><input type="number" step="0.01" name="items[0][length]" placeholder="{{ __('made_to_order::messages.length_cm') }}" class="form-control mb-1" /></div>
                    <div class="col-md-3"><input type="number" step="0.01" name="items[0][width]" placeholder="{{ __('made_to_order::messages.width_cm') }}" class="form-control mb-1" /></div>
                    <div class="col-md-3"><input type="number" step="0.01" name="items[0][height]" placeholder="{{ __('made_to_order::messages.height_cm') }}" class="form-control mb-1" /></div>
                    <div class="col-md-3"><input type="number" step="0.01" name="items[0][thickness]" placeholder="{{ __('made_to_order::messages.thickness') }}" class="form-control mb-1" /></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><input type="number" name="items[0][quantity]" value="1" class="form-control mb-1" /></div>
                    <div class="col-md-6">
                        <select name="items[0][material_id]" class="form-control mb-1">
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }}) - {{ number_format($material->base_price,4) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <textarea name="items[0][notes]" class="form-control mt-2" placeholder="{{ __('made_to_order::messages.notes') }}"></textarea>
            </div>
        </div>
        <button type="button" id="add-item" class="btn btn-secondary mb-3">{{ __('made_to_order::messages.add_item') }}</button>

        <button class="btn btn-primary">{{ __('made_to_order::messages.save_order') }}</button>
    </form>
</div>

<script>
    document.getElementById('add-item').addEventListener('click', function(){
        var container = document.getElementById('items-area');
        var index = container.querySelectorAll('.item-row').length;
        var div = document.createElement('div');
        div.className = 'item-row mb-3 border p-3 rounded';
        div.innerHTML = `
            <input type="text" name="items[${index}][item_name]" placeholder="{{ __('made_to_order::messages.product_name') }}" class="form-control mb-1" />
            <div class="row">
                <div class="col-md-3"><input type="number" step="0.01" name="items[${index}][length]" placeholder="{{ __('made_to_order::messages.length_cm') }}" class="form-control mb-1" /></div>
                <div class="col-md-3"><input type="number" step="0.01" name="items[${index}][width]" placeholder="{{ __('made_to_order::messages.width_cm') }}" class="form-control mb-1" /></div>
                <div class="col-md-3"><input type="number" step="0.01" name="items[${index}][height]" placeholder="{{ __('made_to_order::messages.height_cm') }}" class="form-control mb-1" /></div>
                <div class="col-md-3"><input type="number" step="0.01" name="items[${index}][thickness]" placeholder="{{ __('made_to_order::messages.thickness') }}" class="form-control mb-1" /></div>
            </div>
            <div class="row">
                <div class="col-md-6"><input type="number" name="items[${index}][quantity]" value="1" class="form-control mb-1" /></div>
                <div class="col-md-6">
                    <select name="items[${index}][material_id]" class="form-control mb-1">
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }}) - {{ number_format($material->base_price,4) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <textarea name="items[${index}][notes]" class="form-control mt-2" placeholder="{{ __('made_to_order::messages.notes') }}"></textarea>
        `;
        container.appendChild(div);
    });
</script>
@endsection
