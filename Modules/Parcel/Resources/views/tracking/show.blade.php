<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Parcel {{ $parcel->waybill_number }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; min-height: 100vh; }
        .hero { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white; padding: 32px 24px; text-align: center; }
        .hero h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .hero p { font-size: 14px; color: #94a3b8; }
        .waybill-chip { display: inline-block; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 4px 16px; font-size: 16px; font-weight: 700; letter-spacing: 0.05em; margin-top: 8px; }
        .container { max-width: 680px; margin: 0 auto; padding: 24px 16px; }
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 16px; }
        .card-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-item label { font-size: 11px; color: #94a3b8; display: block; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.04em; }
        .info-item span { font-size: 14px; font-weight: 500; color: #1e293b; }
        /* Timeline */
        .timeline { position: relative; padding: 8px 0; }
        .timeline-step { display: flex; gap: 16px; padding-bottom: 24px; position: relative; }
        .timeline-step:last-child { padding-bottom: 0; }
        .step-indicator { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
        .step-dot { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 16px; }
        .step-line { width: 2px; flex: 1; margin-top: 4px; min-height: 20px; }
        .step-dot.done { background: #059669; color: white; }
        .step-dot.current { background: #f59e0b; color: white; animation: pulse-dot 2s ease-in-out infinite; }
        .step-dot.pending { background: #e2e8f0; color: #94a3b8; }
        .step-dot.failed { background: #ef4444; color: white; }
        .step-line.done { background: #059669; }
        .step-line.pending { background: #e2e8f0; }
        .step-content { padding-top: 6px; flex: 1; }
        .step-title { font-size: 15px; font-weight: 600; }
        .step-title.done { color: #059669; }
        .step-title.current { color: #d97706; }
        .step-title.pending { color: #94a3b8; }
        .step-title.failed { color: #ef4444; }
        .step-meta { font-size: 12px; color: #94a3b8; margin-top: 2px; }
        .step-notes { font-size: 13px; color: #475569; margin-top: 6px; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border-left: 3px solid #e2e8f0; }
        @keyframes pulse-dot { 0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.4); } 50% { box-shadow: 0 0 0 8px rgba(245,158,11,0); } }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .status-booked { background: #dbeafe; color: #1d4ed8; }
        .status-in_transit { background: #fef3c7; color: #d97706; }
        .status-arrived { background: #d1fae5; color: #059669; }
        .status-collected { background: #a7f3d0; color: #065f46; }
        .status-failed { background: #fee2e2; color: #dc2626; }
        .track-another { margin-top: 24px; text-align: center; }
        .track-another form { display: flex; gap: 8px; max-width: 360px; margin: 0 auto; }
        .track-another input { flex: 1; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
        .track-another input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .track-another button { padding: 10px 20px; background: #1e293b; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .track-another button:hover { background: #334155; }
    </style>
</head>
<body>
<div class="hero">
    <h1>📦 Parcel Tracking</h1>
    <p>Real-time status of your shipment</p>
    <div class="waybill-chip">{{ $parcel->waybill_number }}</div>
</div>

<div class="container">
    {{-- Current Status Banner --}}
    <div class="card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:12px;color:#94a3b8;margin-bottom:4px;">CURRENT STATUS</div>
            <span class="status-badge status-{{ $parcel->status }}">
                @php
                    $statusLabels = ['booked'=>'Booked','in_transit'=>'In Transit','arrived'=>'Arrived at Destination','collected'=>'Collected','failed'=>'Delivery Failed'];
                    $statusIcons = ['booked'=>'🔵','in_transit'=>'🚌','arrived'=>'🏁','collected'=>'✅','failed'=>'❌'];
                @endphp
                {{ $statusIcons[$parcel->status] ?? '📦' }} {{ $statusLabels[$parcel->status] ?? ucfirst($parcel->status) }}
            </span>
        </div>
        <div style="text-align:right;">
            <div style="font-size:12px;color:#94a3b8;">LAST UPDATE</div>
            <div style="font-size:13px;font-weight:500;">{{ $parcel->updated_at->format('d M Y, H:i') }}</div>
        </div>
    </div>

    {{-- Route Info --}}
    <div class="card">
        <div class="card-title">Route</div>
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="flex:1;text-align:center;">
                <div style="font-size:20px;">🏠</div>
                <div style="font-size:13px;font-weight:600;margin-top:4px;">{{ $parcel->origin->name ?? 'Origin' }}</div>
                <div style="font-size:11px;color:#94a3b8;">Origin</div>
            </div>
            <div style="flex:1;text-align:center;font-size:20px;color:#94a3b8;">✈️ ──────</div>
            <div style="flex:1;text-align:center;">
                <div style="font-size:20px;">📍</div>
                <div style="font-size:13px;font-weight:600;margin-top:4px;">{{ $parcel->destination->name ?? 'Destination' }}</div>
                <div style="font-size:11px;color:#94a3b8;">Destination</div>
            </div>
        </div>
    </div>

    {{-- Sender/Recipient --}}
    <div class="card">
        <div class="card-title">Shipment Details</div>
        <div class="info-grid">
            <div class="info-item"><label>Sender</label><span>{{ $parcel->sender_name }}</span></div>
            <div class="info-item"><label>Recipient</label><span>{{ $parcel->recipient_name }}</span></div>
            <div class="info-item"><label>Weight</label><span>{{ $parcel->weight_kg }} kg</span></div>
            <div class="info-item"><label>Payment</label><span>{{ ucfirst($parcel->payment_status) }}</span></div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="card">
        <div class="card-title">Journey Timeline</div>
        @php
            $allSteps = [
                'booked'     => ['label' => 'Parcel Booked',          'icon' => '📋'],
                'in_transit' => ['label' => 'In Transit',             'icon' => '🚌'],
                'arrived'    => ['label' => 'Arrived at Destination', 'icon' => '🏁'],
                'collected'  => ['label' => 'Collected by Recipient', 'icon' => '✅'],
            ];
            $statusOrder = ['booked', 'in_transit', 'arrived', 'collected'];
            $currentIndex = array_search($parcel->status, $statusOrder);
            $isFailed = $parcel->status === 'failed';
        @endphp

        <div class="timeline">
            @foreach($allSteps as $stepStatus => $stepInfo)
                @php
                    $stepIndex = array_search($stepStatus, $statusOrder);
                    if ($isFailed && $stepIndex > 0 && $currentIndex < $stepIndex) {
                        $dotClass = 'failed';
                    } elseif ($stepIndex < $currentIndex) {
                        $dotClass = 'done';
                    } elseif ($stepIndex == $currentIndex) {
                        $dotClass = $isFailed ? 'failed' : 'current';
                    } else {
                        $dotClass = 'pending';
                    }
                    // Find the status log for this step
                    $log = $parcel->status_logs->where('status', $stepStatus)->first();
                    $isLast = $loop->last;
                @endphp
                <div class="timeline-step">
                    <div class="step-indicator">
                        <div class="step-dot {{ $dotClass }}">{{ $stepInfo['icon'] }}</div>
                        @if(!$isLast)<div class="step-line {{ $dotClass === 'done' ? 'done' : 'pending' }}"></div>@endif
                    </div>
                    <div class="step-content">
                        <div class="step-title {{ $dotClass }}">{{ $stepInfo['label'] }}</div>
                        @if($log)
                            <div class="step-meta">
                                {{ $log->created_at->format('d M Y, H:i') }}
                                @if($log->station) · {{ $log->station->name }}@endif
                            </div>
                            @if($log->notes)
                                <div class="step-notes">{{ $log->notes }}</div>
                            @endif
                        @else
                            <div class="step-meta">{{ $dotClass === 'pending' ? 'Pending' : 'No record' }}</div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($isFailed)
                <div class="timeline-step">
                    <div class="step-indicator">
                        <div class="step-dot failed">❌</div>
                    </div>
                    <div class="step-content">
                        <div class="step-title failed">Delivery Failed</div>
                        @php $failLog = $parcel->status_logs->where('status', 'failed')->first(); @endphp
                        @if($failLog)
                            <div class="step-meta">{{ $failLog->created_at->format('d M Y, H:i') }}</div>
                            @if($failLog->notes)<div class="step-notes">{{ $failLog->notes }}</div>@endif
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Track Another --}}
    <div class="track-another">
        <p style="font-size:13px;color:#94a3b8;margin-bottom:12px;">Track another parcel</p>
        <form action="{{ url('/track') }}" method="GET">
            <input type="text" name="waybill" placeholder="Enter waybill number..." maxlength="30">
            <button type="submit">Track</button>
        </form>
    </div>
</div>
</body>
</html>
