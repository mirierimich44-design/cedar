@extends('layouts.app')
@section('title', __('Members'))

@section('css')
@parent
<style>
    .coming-soon-container {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .coming-soon-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 3rem 4rem;
        text-align: center;
        max-width: 600px;
        width: 100%;
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06),
            0 20px 50px -12px rgba(79, 70, 229, 0.15);
        border: 1px solid rgba(79, 70, 229, 0.1);
        position: relative;
        overflow: hidden;
    }

    .coming-soon-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4f46e5, #7c3aed, #a855f7);
    }

    .coming-soon-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 2rem;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse-glow 2s ease-in-out infinite;
    }

    .coming-soon-icon svg {
        width: 60px;
        height: 60px;
        color: #4f46e5;
    }

    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.3);
        }
        50% {
            box-shadow: 0 0 0 20px rgba(79, 70, 229, 0);
        }
    }

    .coming-soon-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #fff;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 1.5rem;
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    .coming-soon-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .coming-soon-subtitle {
        font-size: 1.125rem;
        color: #6b7280;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .feature-list {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
        text-align: left;
        margin-bottom: 2.5rem;
        padding: 1.5rem;
        background: #f9fafb;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        color: #374151;
        font-size: 0.9375rem;
    }

    .feature-item .icon-check {
        width: 24px;
        height: 24px;
        min-width: 24px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .feature-item .icon-check svg {
        width: 14px;
        height: 14px;
        color: #fff;
    }

    .coming-soon-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .coming-soon-footer svg {
        width: 18px;
        height: 18px;
        animation: spin 4s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* Floating decorative elements */
    .decoration {
        position: absolute;
        border-radius: 50%;
        opacity: 0.5;
    }

    .decoration-1 {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #c7d2fe 0%, #e0e7ff 100%);
        top: -30px;
        right: -30px;
    }

    .decoration-2 {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        bottom: 20px;
        left: -20px;
    }

    @media (max-width: 640px) {
        .coming-soon-card {
            padding: 2rem 1.5rem;
            margin: 1rem;
        }

        .coming-soon-title {
            font-size: 1.75rem;
        }

        .coming-soon-icon {
            width: 100px;
            height: 100px;
        }

        .coming-soon-icon svg {
            width: 50px;
            height: 50px;
        }
    }
</style>
@endsection

@section('content')
<section class="coming-soon-container">
    <div class="coming-soon-card">
        <!-- Decorative elements -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>

        <!-- Icon -->
        <div class="coming-soon-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>

        <!-- Badge -->
        <div class="coming-soon-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            {{ __('Coming Soon') }}
        </div>

        <!-- Title -->
        <h1 class="coming-soon-title">{{ __('Members Management') }}</h1>

        <!-- Subtitle -->
        <p class="coming-soon-subtitle">
            {{ __('We\'re building something amazing! The Members feature will help you manage your customer base more effectively.') }}
        </p>

        <!-- Features list -->
        <div class="feature-list">
            <div class="feature-item">
                <span class="icon-check">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span>{{ __('Member registration and profiles') }}</span>
            </div>
            <div class="feature-item">
                <span class="icon-check">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span>{{ __('Loyalty points and rewards tracking') }}</span>
            </div>
            <div class="feature-item">
                <span class="icon-check">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span>{{ __('Membership tiers and benefits') }}</span>
            </div>
            <div class="feature-item">
                <span class="icon-check">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span>{{ __('Purchase history and analytics') }}</span>
            </div>
            <div class="feature-item">
                <span class="icon-check">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span>{{ __('Special member discounts and promotions') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="coming-soon-footer">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v4"></path>
                <path d="M12 18v4"></path>
                <path d="M4.93 4.93l2.83 2.83"></path>
                <path d="M16.24 16.24l2.83 2.83"></path>
                <path d="M2 12h4"></path>
                <path d="M18 12h4"></path>
                <path d="M4.93 19.07l2.83-2.83"></path>
                <path d="M16.24 7.76l2.83-2.83"></path>
            </svg>
            <span>{{ __('Under Development') }}</span>
        </div>
    </div>
</section>
@endsection
