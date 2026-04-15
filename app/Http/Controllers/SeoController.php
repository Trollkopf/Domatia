<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Setting;
use App\Models\Zona;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $settingsLastModified = Setting::query()->max('updated_at') ?? now();

        $pages = collect([
            [
                'loc' => url('/'),
                'lastmod' => $settingsLastModified,
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('guest.properties.index'),
                'lastmod' => Property::query()->where('status', 'published')->max('updated_at') ?? now(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('environment'),
                'lastmod' => Zona::query()->max('updated_at') ?? now(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('about'),
                'lastmod' => $settingsLastModified,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('contact'),
                'lastmod' => $settingsLastModified,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
        ]);

        $properties = Property::query()
            ->where('status', 'published')
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get()
            ->map(fn (Property $property) => [
                'loc' => route('guest.property.show', $property->slug),
                'lastmod' => $property->updated_at ?? now(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);

        $zonas = Zona::query()
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get()
            ->map(fn (Zona $zona) => [
                'loc' => route('zonas.show', $zona->slug),
                'lastmod' => $zona->updated_at ?? now(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $urls = $pages
            ->concat($properties)
            ->concat($zonas)
            ->values();

        return response()
            ->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $shouldDisallow = app()->environment('local') || config('app.debug');

        $rules = [
            'disallow_all' => $shouldDisallow,
            'sitemap_url' => route('seo.sitemap'),
            'crawl_delay' => Setting::getValue('seo_crawl_delay'),
        ];

        return response()
            ->view('seo.robots', $rules)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
