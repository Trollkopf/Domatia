<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected array $editableKeys = [
        'company_name',
        'company_phone',
        'company_email',
        'company_address',
        'footer_text',
        'contact_intro',
        'about_heading',
        'about_body',
        'home_hero_image_1',
        'home_hero_image_2',
        'home_hero_image_3',
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
        'home_cta_primary_url',
        'home_cta_secondary_text',
        'home_cta_secondary_url',
    ];

    public function index()
    {
        $settings = [];

        foreach ($this->editableKeys as $key) {
            $settings[$key] = Setting::getValue($key, '');
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_address' => 'nullable|string|max:1000',
            'footer_text' => 'nullable|string|max:1000',
            'contact_intro' => 'nullable|string|max:2000',
            'about_heading' => 'nullable|string|max:255',
            'about_body' => 'nullable|string|max:5000',
            'home_hero_image_1' => 'nullable|string|max:1000',
            'home_hero_image_2' => 'nullable|string|max:1000',
            'home_hero_image_3' => 'nullable|string|max:1000',
            'home_hero_badge' => 'nullable|string|max:255',
            'home_hero_title' => 'nullable|string|max:255',
            'home_hero_subtitle' => 'nullable|string|max:500',
            'home_search_button_text' => 'nullable|string|max:100',
            'home_value_1' => 'nullable|string|max:255',
            'home_value_2' => 'nullable|string|max:255',
            'home_value_3' => 'nullable|string|max:255',
            'home_featured_heading' => 'nullable|string|max:255',
            'home_featured_subtitle' => 'nullable|string|max:500',
            'home_cta_heading' => 'nullable|string|max:255',
            'home_cta_body' => 'nullable|string|max:2000',
            'home_cta_primary_text' => 'nullable|string|max:100',
            'home_cta_primary_url' => 'nullable|string|max:1000',
            'home_cta_secondary_text' => 'nullable|string|max:100',
            'home_cta_secondary_url' => 'nullable|string|max:1000',
        ]);

        foreach ($this->editableKeys as $key) {
            Setting::setValue($key, $validated[$key] ?? null);
        }

        return redirect()->route('admin.settings')->with('success', 'Ajustes actualizados correctamente.');
    }
}
