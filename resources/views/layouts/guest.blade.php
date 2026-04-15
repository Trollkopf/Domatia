<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
        $siteName = $siteSettings['company_name'] ?? config('app.name', 'Domatia');
        $pageTitle = trim($__env->yieldContent('title', $siteName));
        $titleSuffix = trim($siteSettings['seo_title_suffix'] ?? '') ?: $siteName;
        $metaTitleBase = trim($__env->yieldContent('meta_title', $pageTitle));
        $metaTitle = $titleSuffix !== '' && ! \Illuminate\Support\Str::contains($metaTitleBase, $titleSuffix)
            ? $metaTitleBase . ' | ' . $titleSuffix
            : $metaTitleBase;
        $defaultMetaDescription = trim($siteSettings['seo_default_description'] ?? '') ?: ($siteSettings['home_hero_subtitle'] ?? $siteName);
        $metaDescription = trim($__env->yieldContent('meta_description', $defaultMetaDescription));
        $metaImage = trim($__env->yieldContent('meta_image', asset('images/our-company.jpg')));
        $metaType = trim($__env->yieldContent('meta_type', 'website'));
        $metaRobots = trim($__env->yieldContent('meta_robots', 'index,follow'));
        $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
        $twitterCard = trim($__env->yieldContent('twitter_card', 'summary_large_image'));
        $locale = app()->getLocale();
        $ogLocale = str_replace('-', '_', str_replace('_', '-', app()->getLocale()));
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateAgent',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => asset('images/our-company.jpg'),
            'image' => $metaImage,
            'telephone' => $siteSettings['company_phone'] ?: null,
            'email' => $siteSettings['company_email'] ?: null,
            'address' => $siteSettings['company_address']
                ? [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $siteSettings['company_address'],
                ]
                : null,
        ];
        $organizationSchema = array_filter($organizationSchema, fn ($value) => filled($value));
    @endphp
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}" />
    <meta name="robots" content="{{ $metaRobots }}" />
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    @if (! empty($siteSettings['seo_google_verification']))
        <meta name="google-site-verification" content="{{ $siteSettings['seo_google_verification'] }}" />
    @endif
    @if (! empty($siteSettings['seo_bing_verification']))
        <meta name="msvalidate.01" content="{{ $siteSettings['seo_bing_verification'] }}" />
    @endif

    <meta property="og:type" content="{{ $metaType }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    <meta property="og:locale" content="{{ $ogLocale }}" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDescription }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:image" content="{{ $metaImage }}" />

    <meta name="twitter:card" content="{{ $twitterCard }}" />
    <meta name="twitter:title" content="{{ $metaTitle }}" />
    <meta name="twitter:description" content="{{ $metaDescription }}" />
    <meta name="twitter:image" content="{{ $metaImage }}" />
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('style', '')
    @yield('styles', '')

    <style>
        body {
            font-family: 'Titillium Web', sans-serif;
            background-color: #fff;
            color: #222;
        }

        h1,
        h2,
        h3 {
            font-weight: 300;
        }

        a {
            text-decoration: none;
        }

        .page-hero {
            position: relative;
            min-height: 50vh;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .page-hero.page-hero-lg {
            min-height: 60vh;
        }

        .page-hero-media,
        .page-hero img.page-hero-media {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .page-hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.22) 0%, rgba(15, 23, 42, 0.55) 45%, rgba(15, 23, 42, 0.82) 100%),
                linear-gradient(90deg, rgba(17, 24, 39, 0.68) 0%, rgba(17, 24, 39, 0.28) 52%, rgba(17, 24, 39, 0.54) 100%);
        }

        .page-hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .page-hero-copy {
            max-width: 760px;
            padding: 1.5rem 1.75rem;
            border-radius: 24px;
            color: #fff;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.26) 0%, rgba(15, 23, 42, 0.58) 100%);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.24);
        }

        .page-hero-copy h1,
        .page-hero-copy p,
        .page-hero-copy .lead {
            text-shadow: 0 3px 18px rgba(0, 0, 0, 0.32);
            margin-bottom: 0;
        }

        .page-hero-copy p + p,
        .page-hero-copy h1 + p,
        .page-hero-copy h1 + .lead {
            margin-top: 0.75rem;
        }

        @media (max-width: 767.98px) {
            .page-hero,
            .page-hero.page-hero-lg {
                min-height: 42vh;
            }

            .page-hero-copy {
                padding: 1.25rem;
                border-radius: 20px;
            }
        }
    </style>

    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @stack('structured_data')
</head>

<body>
    @include('layouts.navigation')

    <main class="pb-4">
        @yield('content')
        @yield('slider')
    </main>

    <footer class="text-center text-muted border-top py-4 mt-5">
        &copy; {{ date('Y') }} {{ $siteSettings['company_name'] }}. {{ $siteSettings['footer_text'] }}
    </footer>

    @stack('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
