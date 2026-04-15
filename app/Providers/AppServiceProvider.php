<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $translatableSettingKeys = [
        'footer_text',
        'contact_intro',
        'about_heading',
        'about_body',
        'home_hero_badge',
        'home_hero_title',
        'home_hero_subtitle',
        'home_search_button_text',
        'home_value_1',
        'home_value_2',
        'home_value_3',
        'home_featured_heading',
        'home_featured_subtitle',
        'home_cta_heading',
        'home_cta_body',
        'home_cta_primary_text',
        'home_cta_secondary_text',
        'about_header_title',
        'contact_header_title',
        'environment_header_title',
        'register_header_title',
    ];

    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $rawFavoriteCookie = (string) request()->cookie('favorite_properties', '');
            $decodedFavorites = json_decode($rawFavoriteCookie, true);
            $favoritePropertySlugs = collect(is_array($decodedFavorites)
                ? $decodedFavorites
                : ($rawFavoriteCookie === '' ? [] : explode(',', $rawFavoriteCookie)))
                ->filter(fn ($slug) => is_string($slug) && $slug !== '')
                ->unique()
                ->values()
                ->all();
            $setting = function (string $key, string $default = '') {
                if (in_array($key, $this->translatableSettingKeys, true)) {
                    return Setting::getLocalizedValue($key, $default);
                }

                return Setting::getValue($key, $default);
            };

            $view->with('siteSettings', [
                'company_name' => $setting('company_name', 'Domatia'),
                'company_phone' => $setting('company_phone', ''),
                'company_email' => $setting('company_email', ''),
                'company_address' => $setting('company_address', ''),
                'seo_title_suffix' => $setting('seo_title_suffix', ''),
                'seo_default_description' => $setting('seo_default_description', ''),
                'seo_google_verification' => $setting('seo_google_verification', ''),
                'seo_bing_verification' => $setting('seo_bing_verification', ''),
                'seo_crawl_delay' => $setting('seo_crawl_delay', ''),
                'footer_text' => $setting('footer_text', 'Todos los derechos reservados.'),
                'contact_intro' => $setting('contact_intro', 'Estamos aqui para ayudarte a encontrar la propiedad adecuada y resolver cualquier duda.'),
                'about_heading' => $setting('about_heading', 'Nuestra filosofia'),
                'about_body' => $setting('about_body', 'En Domatia nos dedicamos a ofrecer propiedades exclusivas con un enfoque cercano, claro y personalizado para cada cliente.'),
                'home_hero_count' => $setting('home_hero_count', '3'),
                'home_hero_image_1' => $setting('home_hero_image_1', '/images/our-company.jpg'),
                'home_hero_image_2' => $setting('home_hero_image_2', '/images/images.jpg'),
                'home_hero_image_3' => $setting('home_hero_image_3', '/images/our-company.jpg'),
                'home_hero_badge' => $setting('home_hero_badge', 'Seleccion inmobiliaria de confianza'),
                'home_hero_title' => $setting('home_hero_title', 'Descubre propiedades exclusivas'),
                'home_hero_subtitle' => $setting('home_hero_subtitle', 'En los destinos mas deseados'),
                'home_search_button_text' => $setting('home_search_button_text', 'Buscar'),
                'home_value_1' => $setting('home_value_1', 'Seleccion curada de propiedades'),
                'home_value_2' => $setting('home_value_2', 'Acompanamiento cercano de principio a fin'),
                'home_value_3' => $setting('home_value_3', 'Proceso claro y orientado al cierre'),
                'home_featured_heading' => $setting('home_featured_heading', 'Propiedades destacadas'),
                'home_featured_subtitle' => $setting('home_featured_subtitle', 'Una seleccion cuidada para empezar con lo mejor del catalogo.'),
                'home_cta_heading' => $setting('home_cta_heading', 'Te acompanamos en cada paso de la compra'),
                'home_cta_body' => $setting('home_cta_body', 'Desde la primera visita hasta la firma, trabajamos con un enfoque claro, cercano y orientado a cerrar bien cada operacion.'),
                'home_cta_primary_text' => $setting('home_cta_primary_text', 'Ver propiedades'),
                'home_cta_primary_url' => $setting('home_cta_primary_url', route('guest.properties.index')),
                'home_cta_secondary_text' => $setting('home_cta_secondary_text', 'Contactar'),
                'home_cta_secondary_url' => $setting('home_cta_secondary_url', route('contact')),
                'about_header_title' => $setting('about_header_title', 'Conocenos'),
                'about_header_image' => $setting('about_header_image', '/images/our-company.jpg'),
                'contact_header_title' => $setting('contact_header_title', 'Contactanos'),
                'contact_header_image' => $setting('contact_header_image', '/images/our-company.jpg'),
                'environment_header_title' => $setting('environment_header_title', 'Conoce el entorno'),
                'environment_header_image' => $setting('environment_header_image', '/images/images.jpg'),
                'register_header_title' => $setting('register_header_title', 'Crear cuenta'),
                'register_header_image' => $setting('register_header_image', '/images/our-company.jpg'),
            ]);
            $view->with('supportedLocales', config('app.supported_locales', []));
            $view->with('currentLocale', app()->getLocale());
            $view->with('favoritePropertySlugs', $favoritePropertySlugs);
            $view->with('favoritePropertiesCount', count($favoritePropertySlugs));
        });
    }
}
