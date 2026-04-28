@inject('request', 'Illuminate\Http\Request') 

@php
    $whitelist = ['127.0.0.1', '::1']; 
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}" class="no-transition">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ Session::get('business.name') }}</title>
        
        @include('layouts.partials.css') 

        @yield('css')


    </head>

    <body>
        <!-- Add currency related field-->
        <input type="hidden" id="__code" value="{{session('currency')['code']}}">
        <input type="hidden" id="__symbol" value="{{session('currency')['symbol']}}">
        <input type="hidden" id="__thousand" value="{{session('currency')['thousand_separator']}}">
        <input type="hidden" id="__decimal" value="{{session('currency')['decimal_separator']}}">
        <input type="hidden" id="__symbol_placement" value="{{session('business.currency_symbol_placement')}}">
        <input type="hidden" id="__precision" value="{{config('constants.currency_precision', 2)}}">
        <input type="hidden" id="__quantity_precision" value="{{config('constants.quantity_precision', 2)}}">
        <!-- End of currency related field-->
       

    {{--#JCN Using tailwind to make a responsive POS Screen--}}



        <!-- empty div for vuejs --> 
        <div id="app">
            @yield('vue')
        </div>
        
        <!-- yield content has totals-lefside products-rightside and footer in create.blade-->
        @yield('content') 
        


                   
            

            @if(!empty($__additional_html))
                {!! $__additional_html !!}
            @endif

            @include('layouts.partials.javascripts')

            <div class="modal fade view_modal" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel"></div>



            @if(!empty($__additional_views) && is_array($__additional_views))
                @foreach($__additional_views as $additional_view)
                    @includeIf($additional_view)
                @endforeach
            @endif

            
    </body>

</html>
