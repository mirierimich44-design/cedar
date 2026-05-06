@extends('layouts.app')
@section('title', 'Customer Order Links')


@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')

<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div>
                    <h1>Customer Order Links</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<section class="content">
    {{-- Info banner --}}
    <div class="callout callout-info" style="border-left-color:#17a2b8; background:#e3f6f8;">
        <p>
            <i class="fas fa-info-circle"></i>
            <strong>How it works:</strong>
            Each customer gets a unique link. Share it via WhatsApp, SMS or email — they can browse your products, build a cart, and pay via <strong>M-Pesa</strong> or <strong>Cash</strong> without logging in.
        </p>
    </div>

    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-users"></i> Customers</h3>
            <div class="box-tools pull-right">
                <input type="text" id="search_customers" class="form-control input-sm" placeholder="&#x1F50D;  Search customers…" style="width:220px; display:inline-block; border-radius:6px;">
            </div>
        </div>
        <div class="box-body no-padding">
            <table class="table table-hover table-striped" id="customers_table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Link Status</th>
                        <th>Order Link</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                    <tr data-name="{{ strtolower($contact->name) }}" data-phone="{{ $contact->mobile }}">
                        <td>
                            <strong>{{ $contact->name }}</strong>
                            @if($contact->email)
                                <br><small class="text-muted">{{ $contact->email }}</small>
                            @endif
                        </td>
                        <td>{{ $contact->mobile ?? '—' }}</td>
                        <td class="text-center">
                            @if(isset($orderCounts[$contact->id]) && $orderCounts[$contact->id] > 0)
                                <a href="{{ route('orders.index', ['contact_id' => $contact->id]) }}"
                                   class="badge" style="background:#1a73e8; color:#fff;">
                                    {{ $orderCounts[$contact->id] }} order(s)
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($contact->order_token)
                                <span class="label label-success"><i class="fas fa-check"></i> Active</span>
                            @else
                                <span class="label label-default">No link</span>
                            @endif
                        </td>
                        <td>
                            @if($contact->order_token)
                                <div class="input-group input-group-sm" style="max-width:320px;">
                                    <input type="text"
                                           class="form-control order-link-input"
                                           value="{{ url('/customer-order/' . $contact->order_token) }}"
                                           readonly
                                           style="font-size:0.78rem; border-radius:4px 0 0 4px;">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-copy"
                                                data-url="{{ url('/customer-order/' . $contact->order_token) }}"
                                                title="Copy link">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </span>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:0.85rem;">Generate a link first →</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;">
                            @if(!$contact->order_token)
                                <button class="btn btn-sm btn-generate"
                                        style="background:#17a2b8; color:#fff; border:none; border-radius:5px;"
                                        data-contact-id="{{ $contact->id }}"
                                        data-contact-name="{{ $contact->name }}">
                                    <i class="fas fa-link"></i> Generate Link
                                </button>
                            @else
                                {{-- WhatsApp share --}}
                                @if($contact->mobile)
                                @php
                                    $phone   = preg_replace('/\D/', '', $contact->mobile);
                                    if (substr($phone,0,1)==='0') $phone = '254'.substr($phone,1);
                                    if (!str_starts_with($phone,'254')) $phone = '254'.$phone;
                                    $wa_url  = 'https://wa.me/'.$phone.'?text='.urlencode('Hello '.$contact->name.'! Here is your personalised order link: '.url('/customer-order/'.$contact->order_token));
                                @endphp
                                <a href="{{ $wa_url }}" target="_blank"
                                   class="btn btn-sm" style="background:#25D366; color:#fff; border:none; border-radius:5px;">
                                    <i class="fab fa-whatsapp"></i> Share
                                </a>
                                @endif
                                <button class="btn btn-sm btn-regenerate"
                                        style="background:#f0ad4e; color:#fff; border:none; border-radius:5px; margin-left:4px;"
                                        data-contact-id="{{ $contact->id }}"
                                        data-contact-name="{{ $contact->name }}"
                                        title="Generate a new link (old link stops working)">
                                    <i class="fas fa-sync-alt"></i> New Link
                                </button>
                                <a href="{{ url('/customer-order/' . $contact->order_token) }}" target="_blank"
                                   class="btn btn-sm btn-default" style="margin-left:4px;" title="Preview order page">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding:30px;">
                            <i class="fas fa-users" style="font-size:2rem; opacity:0.3;"></i>
                            <p style="margin-top:8px;">No customers found. Add customers in the Contacts section first.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Toast container --}}
<div id="toast_msg" style="display:none; position:fixed; top:20px; right:20px; z-index:9999;
     background:#2e7d32; color:#fff; padding:12px 20px; border-radius:8px;
     box-shadow:0 4px 14px rgba(0,0,0,0.2); font-weight:600; min-width:220px;">
</div>

@endsection

@section('javascript')
<script>
// ── Search ─────────────────────────────────────────────────────────────────
$('#search_customers').on('input', function() {
    var q = $(this).val().toLowerCase();
    $('#customers_table tbody tr').each(function() {
        var name  = $(this).data('name')  || '';
        var phone = ($(this).data('phone') || '').toString().toLowerCase();
        $(this).toggle(!q || name.includes(q) || phone.includes(q));
    });
});

// ── Generate link ──────────────────────────────────────────────────────────
$(document).on('click', '.btn-generate', function() {
    var btn = $(this);
    var id  = btn.data('contact-id');
    var name = btn.data('contact-name');

    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    $.get('{{ url("/contacts") }}/' + id + '/order-token', function(resp) {
        if (resp.success) {
            showToast('✅ Link generated for ' + name);
            setTimeout(function(){ location.reload(); }, 1200);
        } else {
            showToast('❌ Failed to generate link', 'error');
            btn.prop('disabled', false).html('<i class="fas fa-link"></i> Generate Link');
        }
    }).fail(function() {
        showToast('❌ Server error', 'error');
        btn.prop('disabled', false).html('<i class="fas fa-link"></i> Generate Link');
    });
});

// ── Regenerate link ────────────────────────────────────────────────────────
$(document).on('click', '.btn-regenerate', function() {
    var btn  = $(this);
    var id   = btn.data('contact-id');
    var name = btn.data('contact-name');

    if (!confirm('Generate a NEW link for ' + name + '?\n\nThe current link will stop working immediately.')) return;

    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    $.post('{{ url("/contacts") }}/' + id + '/regenerate-order-token',
        { _token: '{{ csrf_token() }}' },
        function(resp) {
            if (resp.success) {
                showToast('✅ New link generated for ' + name);
                setTimeout(function(){ location.reload(); }, 1200);
            } else {
                showToast('❌ Failed', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> New Link');
            }
        }
    ).fail(function() {
        showToast('❌ Server error', 'error');
        btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> New Link');
    });
});

// ── Copy link ──────────────────────────────────────────────────────────────
$(document).on('click', '.btn-copy', function() {
    var url = $(this).data('url');
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            showToast('📋 Link copied to clipboard!');
        });
    } else {
        var $tmp = $('<input>').val(url).appendTo('body').select();
        document.execCommand('copy');
        $tmp.remove();
        showToast('📋 Link copied!');
    }
});

// ── Toast ──────────────────────────────────────────────────────────────────
function showToast(msg, type) {
    var bg = (type === 'error') ? '#c62828' : '#2e7d32';
    $('#toast_msg').css('background', bg).text(msg).fadeIn(200);
    setTimeout(function(){ $('#toast_msg').fadeOut(400); }, 3000);
}
</script>

</div>{{-- .page-modern --}}
@endsection