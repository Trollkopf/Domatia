<?php

use App\Http\Controllers\Admin\ContactoController as AdminContactoController;
use App\Http\Controllers\Admin\KyeroImportController;
use App\Http\Controllers\Admin\PropertyImageController;
use App\Http\Controllers\Admin\PropietarioController;
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
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/dashboard', function () {
    return auth()->user()?->canAccessBackoffice()
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

Route::get('/zonas', [EnvironmentController::class, 'index'])->name('environment');
Route::get('/zonas/{slug}', [EnvironmentController::class, 'show'])->name('zonas.show');

Route::get('/contact', fn() => view('contact.index'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::middleware('backoffice_permission:properties')->group(function () {
        Route::resource('properties', AdminPropertyController::class);
        Route::get('propietarios/search', [PropietarioController::class, 'search'])->name('propietarios.search');
        Route::resource('propietarios', PropietarioController::class)->except(['create', 'show']);
        Route::get('kyero', [KyeroImportController::class, 'index'])->name('kyero.index');
        Route::post('kyero', [KyeroImportController::class, 'store'])->name('kyero.store');
        Route::post('kyero/feeds', [KyeroImportController::class, 'storeFeed'])->name('kyero.feeds.store');
        Route::put('kyero/feeds/{feed}', [KyeroImportController::class, 'updateFeed'])->name('kyero.feeds.update');
        Route::delete('kyero/feeds/{feed}', [KyeroImportController::class, 'destroyFeed'])->name('kyero.feeds.destroy');
        Route::post('kyero/feeds/{feed}/run', [KyeroImportController::class, 'runFeed'])->name('kyero.feeds.run');
        Route::get('kyero/{run}', [KyeroImportController::class, 'show'])->name('kyero.show');
        Route::post('kyero/{run}/process', [KyeroImportController::class, 'process'])->name('kyero.process');
        Route::patch('properties/{property}/images/{image}/set-thumbnail', [PropertyImageController::class, 'setThumbnail'])
            ->name('properties.images.set-thumbnail');
        Route::delete('properties/images/{id}', [PropertyImageController::class, 'destroy'])
            ->name('properties.images.destroy');
    });

    Route::middleware('backoffice_permission:publish_properties')->group(function () {
        Route::patch('property-actions/bulk-publish', [AdminPropertyController::class, 'bulkPublish'])
            ->name('properties.bulk-publish');
        Route::patch('properties/{property}/quick-update', [AdminPropertyController::class, 'quickUpdate'])
            ->name('properties.quick-update');
    });

    Route::middleware('backoffice_permission:zonas')->group(function () {
        Route::resource('zonas', ZonaController::class)->names('zonas');
    });

    Route::middleware('backoffice_permission:contacts')->group(function () {
        Route::get('contactos', [AdminContactoController::class, 'index'])->name('contactos.index');
        Route::get('contactos/{contacto}', [AdminContactoController::class, 'show'])->name('contactos.show');
        Route::put('contactos/{contacto}', [AdminContactoController::class, 'update'])->name('contactos.update');
        Route::patch('contactos/{contacto}/quick-update', [AdminContactoController::class, 'quickUpdate'])->name('contactos.quick-update');
    });

    Route::middleware('manage_users')->group(function () {
        Route::post('users/groups', [UserController::class, 'storeGroup'])->name('users.groups.store');
        Route::patch('users/groups/{group}', [UserController::class, 'updateGroup'])->name('users.groups.update');
        Route::delete('users/groups/{group}', [UserController::class, 'destroyGroup'])->name('users.groups.destroy');
        Route::resource('users', UserController::class)->except('show');
    });

    Route::middleware('backoffice_permission:reports')->group(function () {
        Route::get('reports/export', [ReportController::class, 'export'])
            ->middleware('backoffice_permission:export_reports')
            ->name('reports.export');
        Route::get('reports', [ReportController::class, 'index'])->name('reports');
    });
    Route::middleware('manage_settings')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

require __DIR__ . '/auth.php';
