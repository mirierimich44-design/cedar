@extends('layouts.app')
@section('title', 'Login Screen — ' . $business->name)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Login Screen
        <small class="tw-text-sm tw-text-gray-600">{{ $business->name }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('saas.admin.business') }}">SaaS Admin</a></li>
        <li class="active">{{ $business->name }} — Login Screen</li>
    </ol>
</section>

<section class="content">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-md-7">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Branding Settings</h3></div>
                <div class="box-body">
                    {!! Form::open(['route' => ['saas.admin.business.login-screen.save', $business->id], 'method' => 'POST']) !!}

                    <div class="form-group">
                        <label>Headline</label>
                        <input type="text" name="headline" class="form-control" id="inp_headline"
                            value="{{ $ls['headline'] ?? '' }}"
                            placeholder="e.g. Serengeti Safari Management">
                    </div>

                    <div class="form-group">
                        <label>Tagline</label>
                        <textarea name="tagline" class="form-control" rows="3" id="inp_tagline"
                            placeholder="Short description shown below the headline.">{{ $ls['tagline'] ?? '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Feature Bullets <small class="text-muted">(one per line, emoji first, max 6)</small></label>
                        <textarea name="bullets" class="form-control" rows="6"
                            placeholder="🦁 Safari Bookings&#10;📦 Inventory Management&#10;💰 Revenue Analytics">{{ isset($ls['bullets']) ? implode("\n", $ls['bullets']) : '' }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Accent Colour</label>
                                <input type="color" name="primary_color" class="form-control" id="inp_primary" style="height:42px;"
                                    value="{{ $ls['primary_color'] ?? '#0f766e' }}">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Panel BG — Top</label>
                                <input type="color" name="bg_from" class="form-control" id="inp_bg_from" style="height:42px;"
                                    value="{{ $ls['bg_from'] ?? '#0f4c5c' }}">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Panel BG — Bottom</label>
                                <input type="color" name="bg_to" class="form-control" id="inp_bg_to" style="height:42px;"
                                    value="{{ $ls['bg_to'] ?? '#0a7a62' }}">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    <a href="{{ route('saas.admin.business') }}" class="btn btn-default tw-ml-2">Back</a>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Live Preview</h3></div>
                <div class="box-body" style="padding:0;">
                    <div id="login-preview" style="border-radius:0 0 4px 4px; padding:30px 28px; color:#fff;
                        background: linear-gradient(150deg, {{ $ls['bg_from'] ?? '#0f4c5c' }} 0%, {{ $ls['bg_to'] ?? '#0a7a62' }} 100%);">
                        <div style="font-size:1.2rem; font-weight:800; margin-bottom:10px;" id="preview-headline">
                            {{ $ls['headline'] ?? 'Your Headline Here' }}
                        </div>
                        <div style="opacity:.85; font-size:.9rem; margin-bottom:16px;" id="preview-tagline">
                            {{ $ls['tagline'] ?? 'Your tagline here.' }}
                        </div>
                        <ul style="list-style:none; padding:0; margin:0;" id="preview-bullets">
                            @if(!empty($ls['bullets']))
                                @foreach(array_slice($ls['bullets'], 0, 6) as $b)
                                <li style="margin-bottom:8px; font-size:.88rem;">{{ $b }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function() {
    $('#inp_headline').on('input', function() {
        $('#preview-headline').text($(this).val() || 'Your Headline Here');
    });
    $('#inp_tagline').on('input', function() {
        $('#preview-tagline').text($(this).val() || 'Your tagline here.');
    });
    function updateBg() {
        $('#login-preview').css('background',
            'linear-gradient(150deg, ' + $('#inp_bg_from').val() + ' 0%, ' + $('#inp_bg_to').val() + ' 100%)'
        );
    }
    $('#inp_bg_from, #inp_bg_to').on('input', updateBg);
});
</script>
@endsection
