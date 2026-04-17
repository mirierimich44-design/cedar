@extends('layouts.app')
@section('title', 'Send SMS')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Send SMS</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Send Bulk SMS'])
        @if(session('status'))
            @if(session('status')['success'])
                <div class="alert alert-success">{{ session('status')['msg'] }}</div>
            @else
                <div class="alert alert-danger">{{ session('status')['msg'] }}</div>
            @endif
        @endif

        {!! Form::open(['url' => url('/sms/send'), 'method' => 'post', 'id' => 'sms_send_form']) !!}

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Recipients</label>
                    <select name="recipient_type" id="recipient_type" class="form-control">
                        <option value="manual">Manual — enter phone numbers</option>
                        <option value="all_customers">All Customers</option>
                        <option value="all_suppliers">All Suppliers</option>
                        <option value="group">Customer Group</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row" id="manual_numbers_row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Phone Numbers <small class="text-muted">(comma-separated, e.g. 0712345678, 0723456789)</small></label>
                    <textarea name="mobile_numbers" id="mobile_numbers" class="form-control" rows="3" placeholder="0712345678, 0723456789, ..."></textarea>
                </div>
            </div>
        </div>

        <div class="row hide" id="group_row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Customer Group</label>
                    <select name="customer_group_id" class="form-control">
                        <option value="">-- Select Group --</option>
                        @foreach($customer_groups as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" id="sms_message" class="form-control" rows="5" maxlength="1600" placeholder="Type your message here..."></textarea>
                    <small class="text-muted"><span id="char_count">0</span> / 160 characters
                        <span id="sms_count" class="label label-info" style="margin-left:8px">1 SMS</span>
                    </small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" id="send_btn">
                    <i class="fa fa-paper-plane"></i> Send SMS
                </button>
                <a href="{{ route('sms.history') }}" class="btn btn-default" style="margin-left:8px">
                    <i class="fa fa-history"></i> View History
                </a>
            </div>
        </div>

        {!! Form::close() !!}
    @endcomponent
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Toggle recipient input sections
    $('#recipient_type').change(function() {
        var type = $(this).val();
        $('#manual_numbers_row').addClass('hide');
        $('#group_row').addClass('hide');
        if (type === 'manual') {
            $('#manual_numbers_row').removeClass('hide');
        } else if (type === 'group') {
            $('#group_row').removeClass('hide');
        }
    });

    // Character / SMS counter
    $('#sms_message').on('input', function() {
        var len = $(this).val().length;
        var smsParts = Math.ceil(len / 160) || 1;
        $('#char_count').text(len);
        $('#sms_count').text(smsParts + ' SMS');
    });

    // Confirm before large send
    $('#sms_send_form').submit(function(e) {
        var type = $('#recipient_type').val();
        if (type === 'all_customers' || type === 'all_suppliers') {
            if (!confirm('This will send an SMS to ALL ' + (type === 'all_customers' ? 'customers' : 'suppliers') + '. Are you sure?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
