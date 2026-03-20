<span id="view_contact_page"></span>
<div class="row">
    <div class="col-md-12">
        <div class="col-sm-3">
            @include('contact.contact_basic_info')
        </div>
        <div class="col-sm-3 mt-56">
            @include('contact.contact_more_info')
        </div>
        @if( $contact->type != 'customer')
            <div class="col-sm-3 mt-56">
                @include('contact.contact_tax_info')
            </div>
        @endif
        {{--
        <div class="col-sm-3 mt-56">
            @include('contact.contact_payment_info') 
        </div>
        @if( $contact->type == 'customer' || $contact->type == 'both')
            <div class="col-sm-3 @if($contact->type != 'both') mt-56 @endif">
                <strong>@lang('lang_v1.total_sell_return')</strong>
                <p class="text-muted">
                    <span class="display_currency" data-currency_symbol="true">
                    {{ $contact->total_sell_return }}</span>
                </p>
                <strong>@lang('lang_v1.total_sell_return_due')</strong>
                <p class="text-muted">
                    <span class="display_currency" data-currency_symbol="true">
                    {{ $contact->total_sell_return -  $contact->total_sell_return_paid }}</span>
                </p>
            </div>
        @endif
        --}}

        @if( $contact->type == 'supplier' || $contact->type == 'both')
            <div class="clearfix"></div>
            <div class="col-sm-12">
                @if(($contact->total_purchase - $contact->purchase_paid) > 0)
                    <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$contact->id])}}?type=purchase" class="pay_purchase_due tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right"><i class="fas fa-money-bill-alt" aria-hidden="true"></i> @lang("contact.pay_due_amount")</a>
                @endif
            </div>
        @endif
        <div class="col-sm-12">
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2" data-toggle="modal" data-target="#add_discount_modal">@lang('lang_v1.add_discount')</button>
        </div>

        @if(in_array($contact->type, ['customer', 'both']))
        <div class="col-sm-12" style="margin-top:4px;">
            <button type="button"
                    class="tw-dw-btn tw-dw-btn-sm pull-right tw-m-2"
                    style="background:#17a2b8; color:#fff; border-color:#17a2b8;"
                    onclick="getCustomerOrderLink({{ $contact->id }})">
                <i class="fas fa-link"></i> Order Link
            </button>
        </div>
        @endif
    </div>
</div>

{{-- Customer Order Link Modal --}}
@if(in_array($contact->type, ['customer', 'both']))
<div class="modal fade" id="customer_order_link_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#17a2b8; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
                <h4 class="modal-title"><i class="fas fa-link"></i> Customer Order Link</h4>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted" style="font-size:0.9rem;">Share this link with <strong>{{ $contact->name }}</strong> so they can browse products and place orders directly.</p>
                <div class="input-group" style="margin:12px 0;">
                    <input type="text" id="order_link_url" class="form-control" readonly style="border-radius:6px 0 0 6px; font-size:0.85rem;">
                    <span class="input-group-btn">
                        <button class="btn btn-default" onclick="copyOrderLink()" title="Copy link" style="border-radius:0 6px 6px 0;">
                            <i class="fas fa-copy"></i>
                        </button>
                    </span>
                </div>
                <p id="order_link_copied" style="color:#43a047; display:none; font-size:0.85rem;"><i class="fas fa-check"></i> Copied!</p>
                <hr style="margin:12px 0;">
                <button class="btn btn-xs btn-warning" onclick="regenerateOrderLink({{ $contact->id }})">
                    <i class="fas fa-sync-alt"></i> Generate New Link
                </button>
                <br><small class="text-muted" style="display:block; margin-top:6px;">This invalidates the previous link.</small>
            </div>
            <div class="modal-footer">
                <a id="order_link_open" href="#" target="_blank" class="btn btn-success btn-sm">
                    <i class="fas fa-external-link-alt"></i> Open Link
                </a>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function getCustomerOrderLink(contactId) {
    $.get('{{ url("/contacts") }}/' + contactId + '/order-token', function(resp) {
        if (resp.success) {
            $('#order_link_url').val(resp.order_url);
            $('#order_link_open').attr('href', resp.order_url);
            $('#order_link_copied').hide();
            $('#customer_order_link_modal').modal('show');
        } else {
            alert('Could not generate order link.');
        }
    }).fail(function() {
        alert('Server error generating order link.');
    });
}

function copyOrderLink() {
    var url = $('#order_link_url').val();
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            $('#order_link_copied').show();
            setTimeout(function(){ $('#order_link_copied').hide(); }, 2500);
        });
    } else {
        $('#order_link_url').select();
        document.execCommand('copy');
        $('#order_link_copied').show();
        setTimeout(function(){ $('#order_link_copied').hide(); }, 2500);
    }
}

function regenerateOrderLink(contactId) {
    if (!confirm('Generate a new order link? The current link will stop working.')) return;
    $.post('{{ url("/contacts") }}/' + contactId + '/regenerate-order-token',
        { _token: '{{ csrf_token() }}' },
        function(resp) {
            if (resp.success) {
                $('#order_link_url').val(resp.order_url);
                $('#order_link_open').attr('href', resp.order_url);
                $('#order_link_copied').hide();
                toastr.success('New order link generated!');
            }
        }
    ).fail(function() { alert('Failed to regenerate link.'); });
}
</script>
@endif