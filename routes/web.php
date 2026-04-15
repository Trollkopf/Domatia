<?php

use App\Http\Controllers\Admin\ContactoController as AdminContactoController;
use App\Http\Controllers\Admin\PropertyImageController;
use App\Http\Controllers\Admin\ZonaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PropertyController;
use App\Http\Controllers\PropertyController as AdminPropertyController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/dashboard', function () {
    return auth()->user()?->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('profile.edit');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/buscar', [SearchController::class, 'index'])->name('search');

Route::get('/propiedad/{slug}', [PropertyController::class, 'show'])->name('guest.property.show');
Route::post('/propiedad/{slug}/favorito', [PropertyController::class, 'toggleFavorite'])->name('guest.property.favorite');
Route::get('/propiedades', [PropertyController::class, 'index'])->name('guest.properties.index');
Route::get('/favoritos', [PropertyController::class, 'favorites'])->name('guest.properties.favorites');

Route::get('/nosotros', fn() => view('about.index'))->name('about');

Route::get('/entorno', [EnvironmentController::class, 'index'])->name('environment');
Route::get('/entorno/{slug}', [EnvironmentController::class, 'show'])->name('zonas.show');

Route::get('/contact', fn() => view('contact.index'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::resource('properties', AdminPropertyController::class);
    Route::patch('properties/{property}/quick-update', [AdminPropertyController::class, 'quickUpdate'])
        ->name('properties.quick-update');
    Route::patch('properties/{property}/images/{image}/set-thumbnail', [PropertyImageController::class, 'setThumbnail'])
        ->name('properties.images.set-thumbnail');
    Route::delete('properties/images/{id}', [PropertyImageController::class, 'destroy'])
        ->name('properties.images.destroy');

    Route::resource('zonas', ZonaController::class)->names('zonas');

    Route::get('contactos', [AdminContactoController::class, 'index'])->name('contactos.index');
    Route::get('contactos/{contacto}', [AdminContactoController::class, 'show'])->name('contactos.show');
    Route::put('contactos/{contacto}', [AdminContactoController::class, 'update'])->name('contactos.update');
    Route::patch('contactos/{contacto}/quick-update', [AdminContactoController::class, 'quickUpdate'])->name('contactos.quick-update');

    Route::resource('users', UserController::class);

    Route::get('reports', [ReportController::class, 'index'])->name('reports');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__ . '/auth.php';
