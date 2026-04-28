@extends('layouts.app')
@section('title', __('lang_v1.branch_prices'))

@section('content')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.branch_prices')</h1>
</section>

<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'saveLocationPrices']), 'method' => 'post', 'id' => 'location_price_form']) !!}
    {!! Form::hidden('product_id', $product->id) !!}

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">@lang('sale.product'): {{ $product->name }} ({{ $product->sku }})</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-th-green text-center table-striped">
                            <thead>
                                <tr>
                                    @if($product->type == 'variable')
                                        <th>@lang('lang_v1.variation')</th>
                                    @endif
                                    <th>@lang('lang_v1.default_selling_price_inc_tax')</th>
                                    @foreach($locations as $location_id => $location_name)
                                        <th>{{ $location_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variations as $variation)
                                    <tr>
                                        @if($product->type == 'variable')
                                            <td>
                                                {{ $variation->product_variation->name }} - {{ $variation->name }} ({{ $variation->sub_sku }})
                                            </td>
                                        @endif
                                        <td>
                                            <span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span>
                                        </td>
                                        @foreach($locations as $location_id => $location_name)
                                            <td>
                                                <label style="font-size: 11px; color: #888;">@lang('product.exc_of_tax')</label>
                                                {!! Form::text(
                                                    'location_prices[' . $variation->id . '][' . $location_id . '][default_sell_price]',
                                                    !empty($location_prices[$variation->id][$location_id]['default_sell_price'])
                                                        ? @num_format($location_prices[$variation->id][$location_id]['default_sell_price'])
                                                        : '',
                                                    ['class' => 'form-control input_number input-sm', 'placeholder' => __('product.exc_of_tax')]
                                                ) !!}
                                                <label style="font-size: 11px; color: #888; margin-top: 4px;">@lang('product.inc_of_tax')</label>
                                                {!! Form::text(
                                                    'location_prices[' . $variation->id . '][' . $location_id . '][sell_price_inc_tax]',
                                                    !empty($location_prices[$variation->id][$location_id]['sell_price_inc_tax'])
                                                        ? @num_format($location_prices[$variation->id][$location_id]['sell_price_inc_tax'])
                                                        : '',
                                                    ['class' => 'form-control input_number input-sm', 'placeholder' => __('product.inc_of_tax')]
                                                ) !!}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted" style="margin-top: 8px;">
                        <i class="fa fa-info-circle"></i>
                        @lang('lang_v1.branch_price_note')
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="text-center">
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg">
                    @lang('messages.save')
                </button>
                <a href="{{ url('products') }}" class="tw-dw-btn tw-dw-btn-lg tw-dw-btn-ghost">
                    @lang('messages.cancel')
                </a>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@stop
