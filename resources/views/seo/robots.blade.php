User-agent: *
@if ($disallow_all)
Disallow: /
@else
Allow: /
Disallow: /admin
Disallow: /dashboard
Disallow: /profile
Disallow: /login
Disallow: /register
Disallow: /forgot-password
Disallow: /reset-password
Disallow: /verify-email
Disallow: /favoritos
@if (filled($crawl_delay))
Crawl-delay: {{ $crawl_delay }}
@endif
@endif

Sitemap: {{ $sitemap_url }}
