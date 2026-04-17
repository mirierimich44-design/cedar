@extends('layouts.app')
@section('title', 'DDA Management')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Dangerous Drugs (DDA) Management</h1>
</section>

<section class="content">
    @if(session('status'))
        <div class="alert alert-{{ session('status')['success'] ? 'success' : 'danger' }}">{{ session('status')['msg'] }}</div>
    @endif

    {{-- Stats Row --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="border-left: 4px solid #e74c3c;">
                <span class="info-box-icon" style="background:#e74c3c"><i class="fa fa-pills"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">DDA Products</span>
                    <span class="info-box-number">{{ $stats['dda_products'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="border-left: 4px solid #3498db;">
                <span class="info-box-icon" style="background:#3498db"><i class="fa fa-file-image-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Prescriptions</span>
                    <span class="info-box-number">{{ $stats['prescriptions'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="border-left: 4px solid #2ecc71;">
                <span class="info-box-icon" style="background:#2ecc71"><i class="fa fa-clipboard"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Dispensed Today</span>
                    <span class="info-box-number">{{ $stats['dispensed_today'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="border-left: 4px solid #f39c12;">
                <span class="info-box-icon" style="background:#f39c12"><i class="fa fa-trash"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Destructions</span>
                    <span class="info-box-number">{{ $stats['destructions'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="row" style="margin-bottom:16px">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Quick Actions'])
                <a href="{{ route('dda.prescriptions.create') }}" class="btn btn-danger" style="margin:4px">
                    <i class="fa fa-upload"></i> Upload Prescription
                </a>
                <a href="{{ route('dda.dispense') }}" class="btn btn-primary" style="margin:4px">
                    <i class="fa fa-clipboard"></i> Dispense Register
                </a>
                <a href="{{ route('dda.stock') }}" class="btn btn-success" style="margin:4px">
                    <i class="fa fa-cubes"></i> Stock Balance
                </a>
                <a href="{{ route('dda.destruction') }}" class="btn btn-warning" style="margin:4px">
                    <i class="fa fa-trash"></i> Record Destruction
                </a>
                <a href="{{ route('dda.sales') }}" class="btn btn-info" style="margin:4px">
                    <i class="fa fa-bar-chart"></i> DDA Sales Report
                </a>
            @endcomponent
        </div>
    </div>

    {{-- Recent Dispenses --}}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Recent Dispenses'])
                @if($recent_dispense->isEmpty())
                    <p class="text-muted">No dispense records yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Drug</th>
                                    <th>Patient</th>
                                    <th>Qty</th>
                                    <th>Dispensed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_dispense as $log)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
                                    <td>{{ optional($log->product)->name ?? '—' }}</td>
                                    <td>{{ optional($log->ddaDrug)->name ?? '—' }}</td>
                                    <td>{{ $log->customer_name }}</td>
                                    <td>{{ $log->quantity }} {{ $log->unit }}</td>
                                    <td>{{ optional($log->dispensedBy)->first_name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('dda.dispense') }}" class="btn btn-default btn-sm">View Full Register</a>
                @endif
            @endcomponent
        </div>
    </div>
</section>
@endsection
