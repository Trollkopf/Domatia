<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Setting;
use App\Models\Zona;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $settingsLastModified = Setting::query()->max('updated_at') ?? now();

        $pages = collect([
            $this->makeSitemapEntry(
                url('/'),
                $settingsLastModified,
                'weekly',
                '1.0'
            ),
            $this->makeSitemapEntry(
                route('guest.properties.index'),
                Property::query()->where('status', 'published')->max('updated_at') ?? now(),
                'daily',
                '0.9'
            ),
            $this->makeSitemapEntry(
                route('environment'),
                Zona::query()->max('updated_at') ?? now(),
                'weekly',
                '0.8'
            ),
            $this->makeSitemapEntry(
                route('about'),
                $settingsLastModified,
                'monthly',
                '0.5'
            ),
            $this->makeSitemapEntry(
                route('contact'),
                $settingsLastModified,
                'monthly',
                '0.5'
            ),
        ]);

        $properties = Property::query()
            ->where('status', 'published')
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get()
            ->map(fn (Property $property) => $this->makeSitemapEntry(
                route('guest.property.show', $property->slug),
                $property->updated_at ?? now(),
                'weekly',
                '0.8'
            ));

        $zonas = Zona::query()
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->get()
            ->map(fn (Zona $zona) => $this->makeSitemapEntry(
                route('zonas.show', $zona->slug),
                $zona->updated_at ?? now(),
                'weekly',
                '0.7'
            ));

        $urls = $pages
            ->concat($properties)
            ->concat($zonas)
            ->values();

        return response()
            ->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    protected function makeSitemapEntry(string $loc, mixed $lastmod, string $changefreq, string $priority): array
    {
        $lastModified = $lastmod instanceof Carbon
            ? $lastmod
            : Carbon::parse($lastmod ?: now());

        return [
            'loc' => $loc,
            'lastmod' => $lastModified->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
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
