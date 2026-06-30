@extends('layouts.guest')

@section('title', __('ui.contact.title'))

@php
    $contactSeoTitle = $siteSettings['contact_header_title'] ?: __('ui.contact.title');
    $contactSeoDescription = \Illuminate\Support\Str::limit(
        trim(($siteSettings['contact_intro'] ?? '') . ' ' . ($siteSettings['company_phone'] ?? '') . ' ' . ($siteSettings['company_email'] ?? '')),
        160
    );
@endphp

@section('meta_title', $contactSeoTitle)
@section('meta_description', $contactSeoDescription ?: __('ui.contact.title'))
@section('meta_image', $siteSettings['contact_header_image'] ?: asset('images/our-company.jpg'))
@section('canonical', route('contact'))

@section('style')
<link href="{{ asset('css/contact.css') }}" rel="stylesheet">
<style>
    .contact-map {
        height: 420px;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }
</style>
@endsection

@section('content')
<section class="page-hero page-hero-lg">
    <div class="page-hero-media" style="background: url('{{ $siteSettings['contact_header_image'] }}') no-repeat center center / cover;"></div>
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content">
        <div class="page-hero-copy">
            <h1 class="fw-light">{{ $siteSettings['contact_header_title'] }}</h1>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <h2>{{ __('ui.contact.info_title') }}</h2>
            <p class="text-muted">{{ $siteSettings['contact_intro'] }}</p>
            <div class="d-flex flex-column mb-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-telephone me-3" style="font-size: 1.5rem;"></i>
                    <p class="mb-0">{{ __('frontend.contact.phone_prefix') }} {{ $siteSettings['company_phone'] ?: __('ui.contact.phone_pending') }}</p>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-envelope me-3" style="font-size: 1.5rem;"></i>
                    <p class="mb-0">{{ __('frontend.contact.email_prefix') }} {{ $siteSettings['company_email'] ?: __('ui.contact.email_pending') }}</p>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt me-3" style="font-size: 1.5rem;"></i>
                    <p class="mb-0">{{ $siteSettings['company_address'] ?: __('ui.contact.address_pending') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2>{{ __('ui.contact.message_title') }}</h2>
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('ui.contact.name') }} *</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('ui.contact.name_placeholder') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('ui.contact.email') }} *</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('ui.contact.email_placeholder') }}" required>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">{{ __('ui.contact.message') }} *</label>
                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="{{ __('ui.contact.message_placeholder') }}" required></textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms" required>
                    <label class="form-check-label" for="accept_terms">{{ __('ui.contact.accept_terms') }}</label>
                </div>

                <button type="submit" class="btn btn-main w-100">{{ __('ui.common.send_message') }}</button>
            </form>
        </div>
    </div>
</section>

@if (! empty($siteSettings['company_address']))
    <section class="container pb-5">
        <h2 class="mb-4">{{ __('frontend.contact.map') }}</h2>
        <div
            id="map"
            class="contact-map"
            data-address="{{ $siteSettings['company_address'] }}"
            data-title="{{ $siteSettings['company_name'] ?: 'Domatia' }}"
            data-zoom="15"
        ></div>
    </section>
@endif
@endsection
