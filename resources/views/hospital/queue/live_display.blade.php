<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Room Display</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background-color: #1a1a1a; color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow: hidden; }
        .header { background: #0369a1; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .main-container { display: flex; height: calc(100vh - 80px); }
        .serving-section { flex: 1; border-right: 2px solid #333; padding: 30px; background: #222; }
        .waiting-section { flex: 1; padding: 30px; }
        .section-title { font-size: 24px; color: #94a3b8; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .serving-card { background: #059669; padding: 20px; border-radius: 15px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
        .token-huge { font-size: 72px; font-weight: 900; }
        .patient-name { font-size: 28px; font-weight: 600; }
        .waiting-row { display: flex; justify-content: space-between; padding: 15px; background: #333; margin-bottom: 10px; border-radius: 8px; font-size: 24px; }
        .footer-scroll { position: fixed; bottom: 0; width: 100%; background: #000; padding: 10px; font-size: 20px; color: #fbbf24; }
    </style>
    <meta http-equiv="refresh" content="10">
</head>
<body>
    <div class="header">Hospital Waiting Room Display</div>
    
    <div class="main-container">
        <div class="serving-section">
            <div class="section-title">NOW SERVING</div>
            @foreach($serving as $s)
                <div class="serving-card">
                    <div>
                        <div class="token-huge">{{ $s->token_number }}</div>
                        <div class="patient-name">{{ $s->patient->name }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 20px;">Proceed to:</div>
                        <div style="font-size: 40px; font-weight: 700; color: #fff;">{{ ucfirst($s->current_location) }}</div>
                    </div>
                </div>
            @endforeach
            @if(count($serving) == 0)
                <div style="text-align: center; margin-top: 100px; color: #555;">
                    <i class="fa fa-info-circle" style="font-size: 80px;"></i>
                    <h3>No patient is being served currently</h3>
                </div>
            @endif
        </div>

        <div class="waiting-section">
            <div class="section-title">UP NEXT (WAITING)</div>
            @foreach($waiting as $w)
                <div class="waiting-row">
                    <span style="font-weight: 800; color: #3b82f6;">{{ $w->token_number }}</span>
                    <span>{{ $w->patient->name }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="footer-scroll">
        <marquee>Please keep your token number ready. Thank you for your patience. Standard Consultation fees apply.</marquee>
    </div>
</body>
</html>
