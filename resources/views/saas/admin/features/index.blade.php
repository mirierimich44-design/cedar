@extends('layouts.app')
@section('title', 'Manage Features')
@section('content')
<section class="content-header">
    <h1 class="tw-text-2xl tw-font-bold">Manage Features
        <small>
            <a href="{{ route('saas.admin.features.create') }}" class="btn btn-primary btn-sm" style="margin-left:12px;">
                <i class="fa fa-plus"></i> Add Feature
            </a>
            <a href="{{ route('saas.admin.enquiries') }}" class="btn btn-warning btn-sm" style="margin-left:6px;">
                <i class="fa fa-inbox"></i> Enquiries
            </a>
        </small>
    </h1>
</section>

<style>
.toggle-switch { position:relative; display:inline-block; width:44px; height:24px; vertical-align:middle; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; border-radius:24px; transition:.25s; }
.toggle-slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background:white; border-radius:50%; transition:.25s; }
input:checked + .toggle-slider { background:#00a65a; }
input:checked + .toggle-slider:before { transform:translateX(20px); }
.price-cell { min-width:90px; }
.price-display { cursor:pointer; padding:3px 6px; border-radius:4px; border:1px solid transparent; }
.price-display:hover { border-color:#ddd; background:#f9f9f9; }
.price-input-wrap { display:none; }
.price-input-wrap input { width:80px; height:28px; padding:2px 6px; font-size:12px; border:1px solid #0d9488; border-radius:4px; }
.price-input-wrap .save-price { background:#0d9488; color:white; border:none; border-radius:4px; padding:3px 7px; font-size:11px; cursor:pointer; margin-left:2px; }
.price-input-wrap .cancel-price { background:#aaa; color:white; border:none; border-radius:4px; padding:3px 6px; font-size:11px; cursor:pointer; margin-left:2px; }
.saving-spinner { display:none; color:#0d9488; font-size:12px; margin-left:4px; }
</style>

<section class="content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

@foreach($features as $category => $items)
@component('components.widget', ['header' => ucfirst($category) . ' Features'])
<table class="table table-bordered table-hover" style="font-size:13px;">
    <thead>
        <tr>
            <th>Name</th>
            <th>Key</th>
            <th class="price-cell">Monthly (KES)</th>
            <th class="price-cell">Quarterly (KES)</th>
            <th class="price-cell">Yearly (KES)</th>
            <th class="price-cell">One-Off (KES)</th>
            <th>Required</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($items as $f)
    <tr id="row-{{ $f->id }}">
        <td><strong>{{ $f->name }}</strong>
            @if($f->description)<br><small class="text-muted">{{ Str::limit($f->description,60) }}</small>@endif
        </td>
        <td><code>{{ $f->key }}</code></td>

        {{-- Inline editable price cells --}}
        @foreach(['monthly','quarterly','yearly','once'] as $cycle)
        <td class="price-cell">
            <span class="price-display" onclick="editPrice({{ $f->id }}, '{{ $cycle }}', this)">
                {{ number_format($f->{'price_'.$cycle}, 0) }}
            </span>
            <span class="saving-spinner" id="spin-{{ $f->id }}-{{ $cycle }}"><i class="fa fa-spinner fa-spin"></i></span>
            <div class="price-input-wrap" id="pinput-{{ $f->id }}-{{ $cycle }}">
                <input type="number" value="{{ $f->{'price_'.$cycle} }}" min="0" step="1"
                       id="pval-{{ $f->id }}-{{ $cycle }}">
                <button class="save-price"  onclick="savePrice({{ $f->id }}, '{{ $cycle }}')">✓</button>
                <button class="cancel-price" onclick="cancelPrice({{ $f->id }}, '{{ $cycle }}')">✕</button>
            </div>
        </td>
        @endforeach

        <td>
            @if($f->is_required)
                <span class="label label-primary">Yes</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>

        {{-- Toggle Active --}}
        <td>
            <label class="toggle-switch" title="{{ $f->is_active ? 'Click to disable' : 'Click to enable' }}">
                <input type="checkbox" {{ $f->is_active ? 'checked' : '' }}
                       onchange="toggleFeatureActive({{ $f->id }}, this)">
                <span class="toggle-slider"></span>
            </label>
        </td>

        <td style="white-space:nowrap;">
            <a href="{{ route('saas.admin.features.edit', $f) }}" class="btn btn-xs btn-info" title="Edit all details">
                <i class="fa fa-edit"></i> Edit
            </a>
            <form method="POST" action="{{ route('saas.admin.features.destroy', $f) }}" style="display:inline;"
                  onsubmit="return confirm('Delete {{ addslashes($f->name) }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endcomponent
@endforeach
</section>

<script>
const CSRF = '{{ csrf_token() }}';

/* ── Enable / Disable Toggle ─────────────────────────────── */
function toggleFeatureActive(id, checkbox) {
    const active = checkbox.checked ? 1 : 0;
    fetch(`/saas-admin/features/${id}/toggle`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ is_active: active })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            checkbox.checked = !checkbox.checked; // revert
            alert('Could not update feature status.');
        }
    })
    .catch(() => { checkbox.checked = !checkbox.checked; alert('Network error.'); });
}

/* ── Inline Price Editing ────────────────────────────────── */
function editPrice(id, cycle, displayEl) {
    displayEl.style.display = 'none';
    document.getElementById(`pinput-${id}-${cycle}`).style.display = 'inline-flex';
    document.getElementById(`pval-${id}-${cycle}`).focus();
}

function cancelPrice(id, cycle) {
    document.getElementById(`pinput-${id}-${cycle}`).style.display = 'none';
    document.querySelector(`#row-${id} [onclick="editPrice(${id}, '${cycle}', this)"]`).style.display = '';
}

function savePrice(id, cycle) {
    const val = document.getElementById(`pval-${id}-${cycle}`).value;
    const spin = document.getElementById(`spin-${id}-${cycle}`);

    document.getElementById(`pinput-${id}-${cycle}`).style.display = 'none';
    spin.style.display = 'inline';

    fetch(`/saas-admin/features/${id}/price`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ cycle, price: val })
    })
    .then(r => r.json())
    .then(data => {
        spin.style.display = 'none';
        const displayEl = document.querySelector(`#row-${id} [onclick="editPrice(${id}, '${cycle}', this)"]`);
        if (data.success) {
            displayEl.textContent = Number(val).toLocaleString();
        } else {
            alert('Could not save price.');
        }
        displayEl.style.display = '';
    })
    .catch(() => {
        spin.style.display = 'none';
        cancelPrice(id, cycle);
        alert('Network error.');
    });
}
</script>
@endsection
