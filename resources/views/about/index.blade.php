@extends('layouts.guest')

@section('title', __('ui.about.title'))

@php
    $aboutSeoTitle = $siteSettings['about_header_title'] ?: __('ui.about.title');
    $aboutSeoDescription = \Illuminate\Support\Str::limit(
        trim(($siteSettings['about_heading'] ?? '') . ' ' . ($siteSettings['about_body'] ?? '')),
        160
    );
@endphp

@section('meta_title', $aboutSeoTitle)
@section('meta_description', $aboutSeoDescription ?: __('ui.about.title'))
@section('meta_image', $siteSettings['about_header_image'] ?: asset('images/our-company.jpg'))
@section('canonical', route('about'))

@section('style')
    <link href="{{ asset('css/about.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="page-hero page-hero-lg">
        <div class="page-hero-media" style="background: url('{{ $siteSettings['about_header_image'] }}') no-repeat center center / cover;"></div>
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="page-hero-copy">
                <h1 class="fw-light">{{ $siteSettings['about_header_title'] }}</h1>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <h2 class="text-center mb-4">{{ $siteSettings['about_heading'] }}</h2>
        <p class="lead">
            {{ $siteSettings['about_body'] }}
        </p>
    </section>

    <section class="container py-5">
        <h2 class="text-center mb-4">{{ __('ui.about.contact_title') }}</h2>

        <form action="{{ route('contact.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="name" class="form-label">{{ __('ui.about.name') }} *</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('ui.about.name_placeholder') }}" required>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">{{ __('ui.about.email') }} *</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('ui.about.email_placeholder') }}" required>
            </div>

            <div class="col-12">
                <label for="message" class="form-label">{{ __('ui.about.message') }} *</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="{{ __('ui.about.message_placeholder') }}" required></textarea>
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-main w-50">{{ __('ui.common.send_message') }}</button>
            </div>
        </form>
    </section>
@endsection
