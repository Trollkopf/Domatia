@extends('layouts.guest')

@section('title', __('ui.properties.favorites_title'))
@section('meta_title', __('ui.properties.favorites_title'))
@section('meta_description', __('ui.properties.favorites_intro'))
@section('canonical', route('guest.properties.favorites'))
@section('meta_robots', 'noindex,follow')

@section('style')
    <style>
        :root {
            --favorites-ink: #182230;
            --favorites-muted: #667085;
            --favorites-line: #d9e2ec;
            --favorites-sand: #f7f3eb;
            --favorites-accent: #b88a3b;
            --favorites-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .favorites-page {
            background:
                radial-gradient(circle at top left, rgba(184, 138, 59, 0.16), transparent 24%),
                radial-gradient(circle at top right, rgba(15, 23, 42, 0.06), transparent 22%),
                linear-gradient(180deg, #f7f4ee 0%, #ffffff 38%);
            min-height: calc(100vh - 120px);
        }

        .favorites-hero {
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(255, 248, 237, 0.92)),
                rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(217, 226, 236, 0.92);
            border-radius: 32px;
            box-shadow: var(--favorites-shadow);
        }

        .favorites-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(184, 138, 59, 0.18);
            color: #8a6730;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .favorites-title {
            font-size: clamp(2rem, 2.6vw, 3.1rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
            color: var(--favorites-ink);
            margin: 1rem 0 0.9rem;
            max-width: 11ch;
        }

        .favorites-intro {
            max-width: 60ch;
            color: #526071;
            font-size: 1.02rem;
            margin: 0;
        }

        .favorites-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 11rem;
            padding: 0.9rem 1.2rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--favorites-line);
            color: var(--favorites-ink);
            font-weight: 700;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
        }

        .favorites-shell .property-teaser-card {
            height: 100%;
        }

        .property-teaser-card {
            overflow: hidden;
            border-radius: 28px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
            box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.95);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }

        .property-teaser-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 26px 52px rgba(15, 23, 42, 0.12);
            border-color: rgba(184, 138, 59, 0.28);
        }

        .property-teaser-media {
            position: relative;
            display: block;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #e5e7eb;
        }

        .property-teaser-media::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0) 45%, rgba(15, 23, 42, 0.18) 100%);
            pointer-events: none;
        }

        .property-teaser-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .property-teaser-card:hover .property-teaser-media img {
            transform: scale(1.03);
        }

        .property-teaser-favorite {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 2;
        }

        .property-teaser-favorite .btn {
            width: 2.7rem;
            height: 2.7rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        }

        .property-teaser-body {
            padding: 1.2rem 1.2rem 1.15rem;
        }

        .property-teaser-location {
            color: #8b6a33;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .property-teaser-body h3 a {
            color: var(--favorites-ink);
            text-decoration: none;
        }

        .property-teaser-price {
            white-space: nowrap;
            padding: 0.45rem 0.7rem;
            border-radius: 999px;
            background: var(--favorites-sand);
            color: #6e4f1d;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .property-teaser-summary {
            color: #5f6b7a;
            font-size: 0.94rem;
            line-height: 1.6;
            min-height: 3rem;
        }

        .property-teaser-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.7rem;
            margin-top: 1rem;
        }

        .property-teaser-meta span {
            display: grid;
            gap: 0.15rem;
            padding: 0.75rem 0.8rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #445161;
            font-size: 0.88rem;
            text-align: center;
        }

        .property-teaser-meta strong {
            color: var(--favorites-ink);
            font-size: 0.98rem;
            font-weight: 700;
        }

        .favorites-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: var(--favorites-shadow);
        }

        @media (max-width: 991.98px) {
            .favorites-count {
                min-width: auto;
            }
        }

        @media (max-width: 575.98px) {
            .favorites-hero {
                border-radius: 24px;
            }

            .property-teaser-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="favorites-page py-4 py-lg-5">
        <div class="container">
            <div class="favorites-hero p-4 p-lg-5 mb-4 mb-lg-5">
                <div class="row align-items-end g-4">
                    <div class="col-lg-8">
                        <span class="favorites-kicker">{{ __('frontend.properties.favorites_kicker') }}</span>
                        <h1 class="favorites-title">{{ __('ui.properties.favorites_title') }}</h1>
                        <p class="favorites-intro">
                            {{ __('ui.properties.favorites_intro') }}
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <span class="favorites-count">
                            {{ trans_choice('frontend.properties.favorites_count', $properties->count(), ['count' => $properties->count()]) }}
                        </span>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif

            <section class="favorites-shell">
                @if ($properties->isNotEmpty())
                    <div class="row g-4">
                        @foreach ($properties as $property)
                            <div class="col-md-6 col-xl-4">
                                @include('properties._property-card', ['property' => $property])
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="favorites-empty p-4 p-lg-5 text-center">
                        <h2 class="h4 mb-3">{{ __('ui.properties.favorites_empty_title') }}</h2>
                        <p class="text-muted mb-4">
                            {{ __('ui.properties.favorites_empty_body') }}
                        </p>
                        <a href="{{ route('guest.properties.index') }}" class="btn btn-dark">{{ __('ui.common.explore_properties') }}</a>
                    </div>
                @endif
            </section>
        </div>
    </section>
@endsection
