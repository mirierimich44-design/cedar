@extends('layouts.auth2')
@section('title', config('app.name', 'Reenson'))
@inject('request', 'Illuminate\Http\Request')
@section('content')

{{-- Hero Section --}}
<div class="tw-min-h-screen tw-flex tw-flex-col tw-items-center tw-justify-center tw-px-4 tw-py-16">

    {{-- Logo & Brand --}}
    <div class="tw-flex tw-flex-col tw-items-center tw-mb-10">
        <div class="tw-w-20 tw-h-20 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-xl tw-mb-6">
            <img src="{{ asset('img/logo-small.png') }}" alt="{{ config('app.name') }}" class="tw-w-14 tw-h-14 tw-object-contain" />
        </div>
        <h1 class="tw-text-5xl md:tw-text-6xl tw-font-extrabold tw-text-white tw-text-center tw-tracking-tight tw-drop-shadow-lg">
            {{ config('app.name', 'Reenson') }}
        </h1>
        @if(env('APP_TITLE'))
        <p class="tw-text-xl tw-text-indigo-100 tw-mt-2 tw-text-center tw-font-medium">
            {{ env('APP_TITLE') }}
        </p>
        @else
        <p class="tw-text-xl tw-text-indigo-100 tw-mt-2 tw-text-center tw-font-medium">
            Smart Business Management System
        </p>
        @endif
    </div>

    {{-- CTA Buttons --}}
    <div class="tw-flex tw-flex-wrap tw-gap-4 tw-justify-center tw-mb-16">
        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}"
            class="tw-bg-white tw-text-indigo-600 tw-font-bold tw-text-base tw-px-8 tw-py-3 tw-rounded-full tw-shadow-lg hover:tw-shadow-xl hover:tw-bg-indigo-50 tw-transition-all tw-duration-200">
            Sign In
        </a>
        @if(config('constants.allow_registration'))
        <a href="{{ route('business.getRegister') }}"
            class="tw-bg-transparent tw-border-2 tw-border-white tw-text-white tw-font-bold tw-text-base tw-px-8 tw-py-3 tw-rounded-full hover:tw-bg-white hover:tw-text-indigo-600 tw-transition-all tw-duration-200">
            Get Started
        </a>
        @endif
    </div>

    {{-- Feature Cards --}}
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6 tw-max-w-4xl tw-w-full">

        <div class="tw-bg-white tw-bg-opacity-15 tw-backdrop-blur-sm tw-rounded-2xl tw-p-6 tw-text-center tw-border tw-border-white tw-border-opacity-30 hover:tw-bg-opacity-25 tw-transition-all tw-duration-200">
            <div class="tw-text-3xl tw-mb-3">🛒</div>
            <h3 class="tw-text-white tw-font-bold tw-text-lg tw-mb-2">Point of Sale</h3>
            <p class="tw-text-indigo-100 tw-text-sm">Fast, intuitive POS for retail, restaurants, and service businesses.</p>
        </div>

        <div class="tw-bg-white tw-bg-opacity-15 tw-backdrop-blur-sm tw-rounded-2xl tw-p-6 tw-text-center tw-border tw-border-white tw-border-opacity-30 hover:tw-bg-opacity-25 tw-transition-all tw-duration-200">
            <div class="tw-text-3xl tw-mb-3">📦</div>
            <h3 class="tw-text-white tw-font-bold tw-text-lg tw-mb-2">Inventory Control</h3>
            <p class="tw-text-indigo-100 tw-text-sm">Track stock levels, purchases, and transfers across locations in real time.</p>
        </div>

        <div class="tw-bg-white tw-bg-opacity-15 tw-backdrop-blur-sm tw-rounded-2xl tw-p-6 tw-text-center tw-border tw-border-white tw-border-opacity-30 hover:tw-bg-opacity-25 tw-transition-all tw-duration-200">
            <div class="tw-text-3xl tw-mb-3">📊</div>
            <h3 class="tw-text-white tw-font-bold tw-text-lg tw-mb-2">Reports & Insights</h3>
            <p class="tw-text-indigo-100 tw-text-sm">Comprehensive financial reports, sales analytics, and profit tracking.</p>
        </div>

        <div class="tw-bg-white tw-bg-opacity-15 tw-backdrop-blur-sm tw-rounded-2xl tw-p-6 tw-text-center tw-border tw-border-white tw-border-opacity-30 hover:tw-bg-opacity-25 tw-transition-all tw-duration-200">
            <div class="tw-text-3xl tw-mb-3">👥</div>
            <h3 class="tw-text-white tw-font-bold tw-text-lg tw-mb-2">Customer Management</h3>
            <p class="tw-text-indigo-100 tw-text-sm">Manage customers, suppliers, and contacts with full transaction history.</p>
        </div>

        <div class="tw-bg-white tw-bg-opacity-15 tw-backdrop-blur-sm tw-rounded-2xl tw-p-6 tw-text-center tw-border tw-border-white tw-border-opacity-30 hover:tw-bg-opacity-25 tw-transition-all tw-duration-200">
            <div class="tw-text-3xl tw-mb-3">💰</div>
            <h3 class="tw-text-white tw-font-bold tw-text-lg tw-mb-2">Accounting</h3>
            <p class="tw-text-indigo-100 tw-text-sm">Full accounting module with payments, expenses, and account tracking.</p>
        </div>

        <div class="tw-bg-white tw-bg-opacity-15 tw-backdrop-blur-sm tw-rounded-2xl tw-p-6 tw-text-center tw-border tw-border-white tw-border-opacity-30 hover:tw-bg-opacity-25 tw-transition-all tw-duration-200">
            <div class="tw-text-3xl tw-mb-3">🏢</div>
            <h3 class="tw-text-white tw-font-bold tw-text-lg tw-mb-2">Multi-Location</h3>
            <p class="tw-text-indigo-100 tw-text-sm">Run multiple business locations from a single centralized platform.</p>
        </div>

    </div>

    {{-- Footer --}}
    <p class="tw-text-indigo-200 tw-text-xs tw-mt-12">
        &copy; {{ date('Y') }} {{ config('app.name', 'Reenson') }}. All rights reserved.
    </p>
</div>

@endsection
