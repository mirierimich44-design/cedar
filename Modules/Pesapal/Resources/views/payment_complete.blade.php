@extends('layouts.app')
@section('title', 'Payment Status')

@section('content')
<section class="content" style="padding-top: 40px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box box-solid">
                    <div class="box-header with-border text-center">
                        <h2><i class="fa fa-check-circle text-success"></i> Payment Processed</h2>
                    </div>
                    <div class="box-body text-center">
                        <p class="lead">Thank you — your payment has been submitted.</p>
                        @if($merchantReference)
                            <p><strong>Reference:</strong> <code>{{ $merchantReference }}</code></p>
                        @endif
                        @if($orderTrackingId)
                            <p><strong>Tracking ID:</strong> <code>{{ $orderTrackingId }}</code></p>
                        @endif
                        <p class="text-muted">You can safely close this window. The cashier will see the confirmation automatically.</p>
                        <a href="{{ url('/') }}" class="btn btn-primary">Return home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
