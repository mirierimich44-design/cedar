@extends('layouts.app')
@section('title', 'Edit Order')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Edit Order <small>(Ref: {{ $order->ref_no }})</small></h1>
</section>

<!-- Main content -->
<section class="content">
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status')['msg'] }}
        </div>
    @endif

    <form method="POST" action="{{ action([\App\Http\Controllers\OrderController::class, 'update'], [$order->id]) }}">
        @csrf
        @method('PUT')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Order Details</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('purchase.business_location'):</label>
                            <input type="text" class="form-control" value="{{ $order->location->name ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('contact.customer'):</label>
                            <input type="text" class="form-control" value="{{ $order->contact->name ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="form-group">
                            <label>@lang('sale.status'):</label>
                            <select name="status" class="form-control">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('sale.notes'):</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $order->notes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Order Items</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped" id="pos_order_lines_table">
                    <thead>
                        <tr>
                            <th>@lang('sale.product')</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                            <th><i class="fa fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderLines as $line)
                            <tr class="order-line-row">
                                <td>
                                    {{ $line->product->name ?? $line->custom_product_name }}
                                    @if($line->variation && $line->variation->name != 'DUMMY')
                                        ({{ $line->variation->name }})
                                    @endif
                                </td>
                                <td>
                                    <input type="number" name="order_lines[{{ $line->id }}][quantity]" class="form-control input-sm quantity" value="{{ number_format($line->quantity, 2, '.', '') }}" step="0.01">
                                </td>
                                <td>
                                    <input type="number" name="order_lines[{{ $line->id }}][unit_price]" class="form-control input-sm unit_price" value="{{ number_format($line->unit_price, 2, '.', '') }}" step="0.01">
                                </td>
                                <td>
                                    <span class="subtotal">{{ number_format($line->quantity * $line->unit_price, 2) }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs remove-line"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total:</th>
                            <th id="order_total_display">{{ number_format($order->orderLines->sum(function($line) { return $line->quantity * $line->unit_price; }), 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
            </div>
        </div>
    </form>
</section>

@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        function calculateTotal() {
            var total = 0;
            $('#pos_order_lines_table tbody tr.order-line-row').each(function() {
                var qty = parseFloat($(this).find('.quantity').val()) || 0;
                var price = parseFloat($(this).find('.unit_price').val()) || 0;
                var subtotal = qty * price;
                $(this).find('.subtotal').text(subtotal.toFixed(2));
                total += subtotal;
            });
            $('#order_total_display').text(total.toFixed(2));
        }

        $(document).on('change keyup', '.quantity, .unit_price', function(){
            calculateTotal();
        });

        $(document).on('click', '.remove-line', function(){
            if(confirm("Are you sure you want to remove this item?")) {
                $(this).closest('tr').remove();
                calculateTotal();
            }
        });
    });
</script>
@endsection
