@extends('layouts.app')
@section('title', 'New Patient Bill')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">New Patient Bill</h1>
</section>

<section class="content">
{!! Form::open(['route' => 'hospital-billing.store', 'method' => 'POST', 'id' => 'billing_form']) !!}
<div class="row">
    <!-- LEFT: Patient Info -->
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Patient Information</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Patient Name <span class="text-danger">*</span></label>
                            {!! Form::text('patient_name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Full name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone Number</label>
                            {!! Form::text('patient_phone', null, ['class' => 'form-control', 'placeholder' => '07xxxxxxxx']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date of Birth</label>
                            {!! Form::text('patient_dob', null, ['class' => 'form-control start-date-picker', 'placeholder' => 'DOB', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Gender</label>
                            {!! Form::select('gender', ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'], null, ['class' => 'form-control select2', 'placeholder' => 'Select']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>NHIF Number</label>
                            {!! Form::text('nhif_number', null, ['class' => 'form-control', 'placeholder' => 'NHIF membership no.']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Doctor / Clinician</label>
                            {!! Form::text('doctor_name', null, ['class' => 'form-control', 'placeholder' => 'Attending doctor']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Visit Date <span class="text-danger">*</span></label>
                            {!! Form::text('visit_date', now()->format('Y-m-d'), ['class' => 'form-control start-date-picker', 'required', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Visit Type <span class="text-danger">*</span></label>
                            {!! Form::select('visit_type', $visit_types, null, ['class' => 'form-control select2', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Diagnosis / Chief Complaint</label>
                            {!! Form::textarea('diagnosis', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Patient diagnosis or complaint...']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bill Items -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Bill Items / Services</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-sm btn-success" id="add_bill_item">
                        <i class="fa fa-plus"></i> Add Service
                    </button>
                </div>
            </div>
            <div class="box-body">
                <!-- Quick add buttons -->
                <div class="tw-flex tw-flex-wrap tw-gap-1 tw-mb-3">
                    @foreach($default_services as $svc)
                    <button type="button" class="btn btn-xs btn-default quick-service" data-name="{{ $svc['name'] }}">{{ $svc['name'] }}</button>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="bill_items_table">
                        <thead class="bg-gray">
                            <tr>
                                <th style="width:40%">Service / Description</th>
                                <th style="width:12%">Qty</th>
                                <th style="width:18%">Unit Price (KES)</th>
                                <th style="width:18%">Total (KES)</th>
                                <th style="width:12%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="bill_items_body">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Payment Summary -->
    <div class="col-md-4">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Payment Summary</h3></div>
            <div class="box-body">
                <table class="table table-condensed">
                    <tr><td>Subtotal:</td><td class="text-right"><strong id="display_subtotal">0.00</strong></td></tr>
                    <tr>
                        <td>NHIF Deduction (KES):</td>
                        <td>{!! Form::text('nhif_amount', 0, ['class' => 'form-control input-sm text-right', 'id' => 'nhif_amount']) !!}</td>
                    </tr>
                    <tr>
                        <td>Discount (KES):</td>
                        <td>{!! Form::text('discount', 0, ['class' => 'form-control input-sm text-right', 'id' => 'discount']) !!}</td>
                    </tr>
                    <tr class="bg-light-blue">
                        <td><strong>Total:</strong></td>
                        <td class="text-right"><strong id="display_total">0.00</strong></td>
                    </tr>
                    <tr>
                        <td>Amount Paid (KES):</td>
                        <td>{!! Form::text('paid_amount', 0, ['class' => 'form-control input-sm text-right', 'id' => 'paid_amount']) !!}</td>
                    </tr>
                    <tr class="bg-yellow">
                        <td><strong>Balance:</strong></td>
                        <td class="text-right"><strong id="display_balance">0.00</strong></td>
                    </tr>
                </table>

                <div class="form-group">
                    <label>Payment Method</label>
                    {!! Form::select('payment_method', ['cash' => 'Cash', 'mpesa' => 'M-Pesa', 'nhif' => 'NHIF', 'insurance' => 'Insurance', 'card' => 'Card'], null, ['class' => 'form-control select2', 'id' => 'payment_method']) !!}
                </div>
                <div class="form-group" id="mpesa_code_group" style="display:none">
                    <label>M-Pesa Code</label>
                    {!! Form::text('mpesa_code', null, ['class' => 'form-control', 'placeholder' => 'e.g. QAM1234567']) !!}
                </div>
                <div class="form-group">
                    <label>Location</label>
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => 'Select location']) !!}
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) !!}
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fa fa-save"></i> Save Bill
                </button>
                <a href="{{ route('hospital-billing.index') }}" class="btn btn-default btn-block">Cancel</a>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script>
var itemIndex = 0;

function addRow(name = '', qty = 1, price = 0) {
    var i = itemIndex++;
    var total = (qty * price).toFixed(2);
    var row = `<tr id="item_row_${i}">
        <td><input type="text" name="bill_items[${i}][name]" class="form-control input-sm" value="${name}" placeholder="Service description" required></td>
        <td><input type="number" name="bill_items[${i}][qty]" class="form-control input-sm item-qty" value="${qty}" min="1" step="0.01" data-index="${i}"></td>
        <td><input type="number" name="bill_items[${i}][unit_price]" class="form-control input-sm item-price" value="${price}" min="0" step="0.01" data-index="${i}"></td>
        <td><input type="number" name="bill_items[${i}][total]" class="form-control input-sm item-total text-right" value="${total}" readonly></td>
        <td><button type="button" class="btn btn-xs btn-danger remove-item" data-index="${i}"><i class="fa fa-trash"></i></button></td>
    </tr>`;
    $('#bill_items_body').append(row);
    recalculate();
}

function recalculate() {
    var subtotal = 0;
    $('.item-total').each(function() { subtotal += parseFloat($(this).val()) || 0; });
    var nhif    = parseFloat($('#nhif_amount').val()) || 0;
    var disc    = parseFloat($('#discount').val()) || 0;
    var total   = subtotal - nhif - disc;
    var paid    = parseFloat($('#paid_amount').val()) || 0;
    var balance = total - paid;
    $('#display_subtotal').text(subtotal.toFixed(2));
    $('#display_total').text(total.toFixed(2));
    $('#display_balance').text(balance.toFixed(2));
    if (balance < 0) $('#display_balance').addClass('text-danger'); else $('#display_balance').removeClass('text-danger');
}

$(function() {
    // First row
    addRow('', 1, 0);

    $('#add_bill_item').on('click', function() { addRow(); });

    $(document).on('click', '.quick-service', function() {
        addRow($(this).data('name'), 1, 0);
    });

    $(document).on('change keyup', '.item-qty, .item-price', function() {
        var idx = $(this).data('index');
        var qty   = parseFloat($('input[name="bill_items['+idx+'][qty]"]').val()) || 0;
        var price = parseFloat($('input[name="bill_items['+idx+'][unit_price]"]').val()) || 0;
        $('input[name="bill_items['+idx+'][total]"]').val((qty * price).toFixed(2));
        recalculate();
    });

    $(document).on('click', '.remove-item', function() {
        $('#item_row_' + $(this).data('index')).remove();
        recalculate();
    });

    $('#nhif_amount, #discount, #paid_amount').on('keyup change', recalculate);

    $('#payment_method').on('change', function() {
        $('#mpesa_code_group').toggle($(this).val() === 'mpesa');
    });
});
</script>
@endsection
