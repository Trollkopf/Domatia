<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
            $view->with('siteSettings', [
                'company_name' => Setting::getValue('company_name', 'Domatia'),
                'company_phone' => Setting::getValue('company_phone', ''),
                'company_email' => Setting::getValue('company_email', ''),
                'company_address' => Setting::getValue('company_address', ''),
                'footer_text' => Setting::getValue('footer_text', 'Todos los derechos reservados.'),
                'contact_intro' => Setting::getValue('contact_intro', 'Estamos aqui para ayudarte a encontrar la propiedad adecuada y resolver cualquier duda.'),
                'about_heading' => Setting::getValue('about_heading', 'Nuestra filosofia'),
                'about_body' => Setting::getValue('about_body', 'En Domatia nos dedicamos a ofrecer propiedades exclusivas con un enfoque cercano, claro y personalizado para cada cliente.'),
                'home_hero_image_1' => Setting::getValue('home_hero_image_1', '/images/hero1.jpg'),
                'home_hero_image_2' => Setting::getValue('home_hero_image_2', '/images/hero2.jpg'),
                'home_hero_image_3' => Setting::getValue('home_hero_image_3', '/images/our-company.jpg'),
                'home_hero_badge' => Setting::getValue('home_hero_badge', 'Seleccion inmobiliaria de confianza'),
                'home_hero_title' => Setting::getValue('home_hero_title', 'Descubre propiedades exclusivas'),
                'home_hero_subtitle' => Setting::getValue('home_hero_subtitle', 'En los destinos mas deseados'),
                'home_search_button_text' => Setting::getValue('home_search_button_text', 'Buscar'),
                'home_value_1' => Setting::getValue('home_value_1', 'Seleccion curada de propiedades'),
                'home_value_2' => Setting::getValue('home_value_2', 'Acompanamiento cercano de principio a fin'),
                'home_value_3' => Setting::getValue('home_value_3', 'Proceso claro y orientado al cierre'),
                'home_featured_heading' => Setting::getValue('home_featured_heading', 'Propiedades destacadas'),
                'home_featured_subtitle' => Setting::getValue('home_featured_subtitle', 'Una seleccion cuidada para empezar con lo mejor del catalogo.'),
                'home_cta_heading' => Setting::getValue('home_cta_heading', 'Te acompanamos en cada paso de la compra'),
                'home_cta_body' => Setting::getValue('home_cta_body', 'Desde la primera visita hasta la firma, trabajamos con un enfoque claro, cercano y orientado a cerrar bien cada operacion.'),
                'home_cta_primary_text' => Setting::getValue('home_cta_primary_text', 'Ver propiedades'),
                'home_cta_primary_url' => Setting::getValue('home_cta_primary_url', route('guest.properties.index')),
                'home_cta_secondary_text' => Setting::getValue('home_cta_secondary_text', 'Contactar'),
                'home_cta_secondary_url' => Setting::getValue('home_cta_secondary_url', route('contact')),
            ]);
        });
    }
}
