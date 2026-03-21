@extends('layouts.app')
@section('title', 'Place Customer Order')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-2xl tw-font-bold tw-text-black">Place Customer Order</h1>
</section>

<section class="content">
    <div class="row">
        {{-- Left: customer + products --}}
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border" style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);border:none;">
                    <h3 class="box-title tw-text-white"><i class="fa fa-shopping-cart tw-mr-2"></i>Order Details</h3>
                </div>
                <div class="box-body">
                    {{-- Customer selector --}}
                    <div class="form-group">
                        <label class="tw-font-semibold">Select Customer *</label>
                        <select id="order-customer-select" class="form-control select2" required>
                            <option value="">— Select customer —</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}"
                                data-phone="{{ $c->phone }}"
                                data-name="{{ $c->name }}"
                                {{ $selectedCustomerId == $c->id ? 'selected' : '' }}>
                                {{ $c->outlet_name }} ({{ $c->name }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product search --}}
                    <div class="form-group tw-mt-4">
                        <label class="tw-font-semibold">Search Products</label>
                        <input type="text" id="product-search" class="form-control" placeholder="Type product name...">
                    </div>

                    {{-- Product grid --}}
                    <div id="product-grid" class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-gap-3 tw-mt-3" style="max-height:420px;overflow-y:auto;">
                        @foreach($products as $p)
                        <div class="product-card tw-border tw-border-gray-200 tw-rounded-xl tw-p-3 tw-cursor-pointer tw-transition hover:tw-shadow-md hover:tw-border-theme"
                             data-variation-id="{{ $p->variation_id }}"
                             data-product-name="{{ $p->product_name }}"
                             data-variation-name="{{ $p->variation_name }}"
                             data-price="{{ $p->price }}"
                             style="background:#fff;">
                            <p class="tw-font-semibold tw-text-sm tw-text-gray-800 tw-mb-0 tw-leading-tight">{{ $p->product_name }}</p>
                            @if($p->variation_name !== 'DUMMY')
                                <p class="tw-text-xs tw-text-gray-500 tw-mb-1">{{ $p->variation_name }}</p>
                            @endif
                            <p class="tw-text-sm tw-font-bold tw-mt-1" style="color:var(--theme-main);">KES {{ number_format($p->price, 2) }}</p>
                            <button type="button" class="btn-add-to-cart tw-mt-1 tw-w-full tw-text-xs tw-font-semibold tw-text-white tw-py-1 tw-rounded-lg" style="background:var(--theme-main);">
                                + Add
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Cart + Payment --}}
        <div class="col-md-4">
            <div class="box box-success" id="cart-box">
                <div class="box-header with-border tw-bg-green-600">
                    <h3 class="box-title tw-text-white">Order Cart</h3>
                </div>
                <div class="box-body">
                    {{-- Cart items --}}
                    <div id="cart-items">
                        <p class="tw-text-gray-400 tw-text-sm" id="cart-empty-msg">No items added yet.</p>
                    </div>
                    <hr class="tw-my-2">
                    <div class="tw-flex tw-justify-between tw-font-bold tw-text-gray-800">
                        <span>Total:</span>
                        <span id="cart-total">KES 0.00</span>
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Payment</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label class="tw-font-semibold">Payment Method</label>
                        <div class="tw-flex tw-gap-3 tw-mt-1">
                            @if($mpesaEnabled)
                            <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer tw-font-normal">
                                <input type="radio" name="payment_method" value="mpesa" id="pay-mpesa" checked>
                                <span class="tw-font-semibold tw-text-green-700">MPESA</span>
                            </label>
                            @endif
                            <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer tw-font-normal">
                                <input type="radio" name="payment_method" value="cash" id="pay-cash" {{ !$mpesaEnabled ? 'checked' : '' }}>
                                <span class="tw-font-semibold tw-text-gray-700">Cash</span>
                            </label>
                        </div>
                    </div>

                    @if($mpesaEnabled)
                    <div id="mpesa-phone-group" class="form-group">
                        <label class="tw-font-semibold">Customer MPESA Phone *</label>
                        <input type="text" id="mpesa-phone" class="form-control" placeholder="0712345678">
                        <small class="help-block">Phone to receive the STK push prompt</small>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <textarea id="order-notes" class="form-control" rows="2" placeholder="Delivery notes, special requests..."></textarea>
                    </div>

                    <button type="button" id="place-order-btn"
                            class="btn btn-block btn-lg tw-text-white tw-font-bold tw-rounded-xl"
                            style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);border:none;">
                        <i class="fa fa-check"></i> Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MPESA Polling Modal --}}
    <div class="modal fade" id="mpesa-modal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm tw-max-w-sm" role="document">
            <div class="modal-content tw-rounded-2xl tw-overflow-hidden">
                <div class="modal-header" style="background:linear-gradient(135deg,#00875a,#00c378);border:none;padding:20px;">
                    <h4 class="modal-title tw-text-white tw-font-bold">MPESA Payment</h4>
                </div>
                <div class="modal-body tw-text-center tw-py-6">
                    <div id="mpesa-pending">
                        <div class="tw-animate-spin tw-mx-auto tw-mb-4" style="width:48px;height:48px;border:4px solid #e5e7eb;border-top-color:#00875a;border-radius:50%;"></div>
                        <p class="tw-font-semibold tw-text-gray-800">Waiting for payment...</p>
                        <p id="mpesa-phone-display" class="tw-text-sm tw-text-gray-500 tw-mt-1"></p>
                        <p class="tw-text-xs tw-text-gray-400 tw-mt-3">Check customer's phone for the MPESA prompt.</p>
                    </div>
                    <div id="mpesa-success" class="tw-hidden">
                        <div style="width:56px;height:56px;background:#00875a;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="fa fa-check" style="color:white;font-size:24px;"></i>
                        </div>
                        <p class="tw-font-bold tw-text-green-700 tw-text-lg">Payment Received!</p>
                        <p id="mpesa-receipt" class="tw-text-sm tw-text-gray-600 tw-mt-1"></p>
                        <p id="mpesa-order-ref" class="tw-text-xs tw-text-gray-500 tw-mt-1"></p>
                    </div>
                    <div id="mpesa-failed" class="tw-hidden">
                        <div style="width:56px;height:56px;background:#dc2626;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="fa fa-times" style="color:white;font-size:24px;"></i>
                        </div>
                        <p class="tw-font-bold tw-text-red-600 tw-text-lg">Payment Failed</p>
                        <p class="tw-text-sm tw-text-gray-600 tw-mt-1">The customer cancelled or did not pay.</p>
                    </div>
                </div>
                <div class="modal-footer tw-border-t-0 tw-justify-center">
                    <button type="button" id="mpesa-done-btn" class="btn btn-success tw-hidden">Done</button>
                    <button type="button" id="mpesa-new-order-btn" class="btn btn-primary tw-hidden">Place Another Order</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    var cart   = {};   // { variation_id: { name, price, qty } }
    var orderId = null;
    var mpesaTxId = null;
    var pollTimer = null;

    // ── Cart management ─────────────────────────────────────────────────
    function renderCart() {
        var $items = $('#cart-items');
        var $empty = $('#cart-empty-msg');
        var total  = 0;

        $items.find('.cart-line').remove();

        $.each(cart, function (vid, item) {
            var lineTotal = item.price * item.qty;
            total += lineTotal;
            $items.append(
                '<div class="cart-line tw-flex tw-items-center tw-justify-between tw-mb-2 tw-gap-2">' +
                    '<div class="tw-flex-1 tw-min-w-0">' +
                        '<p class="tw-text-xs tw-font-semibold tw-text-gray-800 tw-truncate">' + item.name + '</p>' +
                        '<p class="tw-text-xs tw-text-gray-500">KES ' + item.price.toFixed(2) + ' × ' + item.qty + '</p>' +
                    '</div>' +
                    '<div class="tw-flex tw-items-center tw-gap-1">' +
                        '<button type="button" class="btn-qty tw-w-6 tw-h-6 tw-text-xs tw-rounded tw-bg-gray-100 tw-font-bold" data-vid="' + vid + '" data-dir="-1">−</button>' +
                        '<span class="tw-text-sm tw-font-bold tw-w-6 tw-text-center">' + item.qty + '</span>' +
                        '<button type="button" class="btn-qty tw-w-6 tw-h-6 tw-text-xs tw-rounded tw-bg-gray-100 tw-font-bold" data-vid="' + vid + '" data-dir="1">+</button>' +
                        '<button type="button" class="btn-remove-cart tw-w-6 tw-h-6 tw-text-xs tw-rounded tw-bg-red-100 tw-text-red-600 tw-font-bold" data-vid="' + vid + '">×</button>' +
                    '</div>' +
                '</div>'
            );
        });

        $empty.toggle($.isEmptyObject(cart));
        $('#cart-total').text('KES ' + total.toFixed(2));
    }

    $(document).on('click', '.btn-add-to-cart', function (e) {
        e.stopPropagation();
        var $card = $(this).closest('.product-card');
        var vid   = $card.data('variation-id');
        var name  = $card.data('product-name') + ($card.data('variation-name') !== 'DUMMY' ? ' – ' + $card.data('variation-name') : '');
        var price = parseFloat($card.data('price'));
        if (cart[vid]) {
            cart[vid].qty++;
        } else {
            cart[vid] = { name: name, price: price, qty: 1 };
        }
        renderCart();
    });

    $(document).on('click', '.btn-qty', function () {
        var vid = $(this).data('vid');
        var dir = parseInt($(this).data('dir'));
        if (!cart[vid]) return;
        cart[vid].qty += dir;
        if (cart[vid].qty <= 0) delete cart[vid];
        renderCart();
    });

    $(document).on('click', '.btn-remove-cart', function () {
        delete cart[$(this).data('vid')];
        renderCart();
    });

    // ── Product search filter ────────────────────────────────────────────
    $('#product-search').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('.product-card').each(function () {
            var name = $(this).data('product-name').toLowerCase() + ' ' + $(this).data('variation-name').toLowerCase();
            $(this).toggle(name.indexOf(q) > -1);
        });
    });

    // ── Toggle MPESA phone field ─────────────────────────────────────────
    $('input[name="payment_method"]').on('change', function () {
        $('#mpesa-phone-group').toggle($(this).val() === 'mpesa');
    });
    // Pre-fill phone when customer is selected
    $('#order-customer-select').on('change', function () {
        var phone = $(this).find('option:selected').data('phone') || '';
        $('#mpesa-phone').val(phone);
    });
    @if($selectedCustomerId)
    $('#order-customer-select').trigger('change');
    @endif

    // ── Place order ──────────────────────────────────────────────────────
    $('#place-order-btn').on('click', function () {
        var customerId     = $('#order-customer-select').val();
        var paymentMethod  = $('input[name="payment_method"]:checked').val();
        var mpesaPhone     = $('#mpesa-phone').val().trim();
        var notes          = $('#order-notes').val();

        if (!customerId) { alert('Please select a customer.'); return; }
        if ($.isEmptyObject(cart)) { alert('Please add at least one product.'); return; }
        if (paymentMethod === 'mpesa' && !mpesaPhone) { alert('Please enter the customer MPESA phone number.'); return; }

        var items = [];
        $.each(cart, function (vid, item) {
            items.push({ variation_id: vid, qty: item.qty });
        });

        var payload = {
            _token:         '{{ csrf_token() }}',
            customer_id:    customerId,
            items:          items,
            payment_method: paymentMethod,
            mpesa_phone:    mpesaPhone,
            notes:          notes,
        };

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Placing...');

        $.post('{{ route('cooler.agent.orders.store') }}', payload, function (res) {
            if (res.success) {
                if (res.mpesa_initiated) {
                    orderId   = res.order_id;
                    mpesaTxId = res.mpesa_transaction_id;
                    showMpesaModal(mpesaPhone, res.ref_no);
                    startPolling();
                } else {
                    alert(res.message || 'Order placed!');
                    resetForm();
                }
            } else {
                alert(res.message || 'Failed to place order.');
            }
        }).fail(function () {
            alert('Server error. Please try again.');
        }).always(function () {
            $('#place-order-btn').prop('disabled', false).html('<i class="fa fa-check"></i> Place Order');
        });
    });

    // ── MPESA Modal ──────────────────────────────────────────────────────
    function showMpesaModal(phone, ref) {
        $('#mpesa-pending').show();
        $('#mpesa-success, #mpesa-failed').addClass('tw-hidden');
        $('#mpesa-done-btn, #mpesa-new-order-btn').addClass('tw-hidden');
        $('#mpesa-phone-display').text('Prompt sent to: ' + phone);
        $('#mpesa-order-ref').text('Order: ' + (ref || ''));
        $('#mpesa-modal').modal('show');
    }

    function startPolling() {
        var attempts = 0;
        pollTimer = setInterval(function () {
            attempts++;
            if (attempts > 20) { // ~100s max
                clearInterval(pollTimer);
                showMpesaFailed();
                return;
            }
            $.get('{{ route('cooler.agent.orders.check_payment') }}', {
                mpesa_transaction_id: mpesaTxId,
                order_id: orderId,
            }, function (res) {
                if (res.is_paid) {
                    clearInterval(pollTimer);
                    showMpesaSuccess(res.receipt_number);
                } else if (res.status === 'failed') {
                    clearInterval(pollTimer);
                    showMpesaFailed();
                }
            });
        }, 5000);
    }

    function showMpesaSuccess(receipt) {
        $('#mpesa-pending').hide();
        $('#mpesa-success').removeClass('tw-hidden');
        $('#mpesa-receipt').text('Receipt: ' + (receipt || '—'));
        $('#mpesa-done-btn, #mpesa-new-order-btn').removeClass('tw-hidden');
    }

    function showMpesaFailed() {
        $('#mpesa-pending').hide();
        $('#mpesa-failed').removeClass('tw-hidden');
        $('#mpesa-new-order-btn').removeClass('tw-hidden');
    }

    $('#mpesa-done-btn, #mpesa-new-order-btn').on('click', function () {
        clearInterval(pollTimer);
        $('#mpesa-modal').modal('hide');
        resetForm();
    });

    function resetForm() {
        cart = {};
        orderId = null;
        mpesaTxId = null;
        renderCart();
        $('#order-customer-select').val(null).trigger('change.select2');
        $('#mpesa-phone').val('');
        $('#order-notes').val('');
        $('#product-search').val('').trigger('input');
    }
});
</script>
@endsection
