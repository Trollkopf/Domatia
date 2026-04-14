@extends('layouts.admin')

@section('title', 'Configuracion')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Configuracion</h1>
            <p class="text-muted mb-0">Gestiona datos base de la empresa y los textos clave del sitio.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <h2 class="h5 mb-1">Datos generales</h2>
                        <p class="text-muted small mb-0">Se reutilizan en navegacion, contacto y footer.</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nombre de empresa</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email comercial</label>
                        <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Direccion</label>
                        <input type="text" name="company_address" class="form-control" value="{{ old('company_address', $settings['company_address']) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto de footer</label>
                        <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', $settings['footer_text']) }}">
                    </div>

                    <div class="col-12 pt-3">
                        <h2 class="h5 mb-1">Portada</h2>
                        <p class="text-muted small mb-0">Controla la propuesta de valor visible en la home.</p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Imagen hero 1</label>
                        <input type="text" name="home_hero_image_1" class="form-control" value="{{ old('home_hero_image_1', $settings['home_hero_image_1']) }}" placeholder="/images/hero1.jpg">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Imagen hero 2</label>
                        <input type="text" name="home_hero_image_2" class="form-control" value="{{ old('home_hero_image_2', $settings['home_hero_image_2']) }}" placeholder="/images/hero2.jpg">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Imagen hero 3</label>
                        <input type="text" name="home_hero_image_3" class="form-control" value="{{ old('home_hero_image_3', $settings['home_hero_image_3']) }}" placeholder="/images/our-company.jpg">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Etiqueta superior hero</label>
                        <input type="text" name="home_hero_badge" class="form-control" value="{{ old('home_hero_badge', $settings['home_hero_badge']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo hero</label>
                        <input type="text" name="home_hero_title" class="form-control" value="{{ old('home_hero_title', $settings['home_hero_title']) }}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Subtitulo hero</label>
                        <input type="text" name="home_hero_subtitle" class="form-control" value="{{ old('home_hero_subtitle', $settings['home_hero_subtitle']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Texto boton buscador</label>
                        <input type="text" name="home_search_button_text" class="form-control" value="{{ old('home_search_button_text', $settings['home_search_button_text']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Argumento 1</label>
                        <input type="text" name="home_value_1" class="form-control" value="{{ old('home_value_1', $settings['home_value_1']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Argumento 2</label>
                        <input type="text" name="home_value_2" class="form-control" value="{{ old('home_value_2', $settings['home_value_2']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Argumento 3</label>
                        <input type="text" name="home_value_3" class="form-control" value="{{ old('home_value_3', $settings['home_value_3']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo destacadas</label>
                        <input type="text" name="home_featured_heading" class="form-control" value="{{ old('home_featured_heading', $settings['home_featured_heading']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Subtitulo destacadas</label>
                        <input type="text" name="home_featured_subtitle" class="form-control" value="{{ old('home_featured_subtitle', $settings['home_featured_subtitle']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo bloque final</label>
                        <input type="text" name="home_cta_heading" class="form-control" value="{{ old('home_cta_heading', $settings['home_cta_heading']) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto bloque final</label>
                        <textarea name="home_cta_body" class="form-control" rows="4">{{ old('home_cta_body', $settings['home_cta_body']) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Texto CTA principal</label>
                        <input type="text" name="home_cta_primary_text" class="form-control" value="{{ old('home_cta_primary_text', $settings['home_cta_primary_text']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL CTA principal</label>
                        <input type="text" name="home_cta_primary_url" class="form-control" value="{{ old('home_cta_primary_url', $settings['home_cta_primary_url']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Texto CTA secundario</label>
                        <input type="text" name="home_cta_secondary_text" class="form-control" value="{{ old('home_cta_secondary_text', $settings['home_cta_secondary_text']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL CTA secundario</label>
                        <input type="text" name="home_cta_secondary_url" class="form-control" value="{{ old('home_cta_secondary_url', $settings['home_cta_secondary_url']) }}">
                    </div>

                    <div class="col-12 pt-3">
                        <h2 class="h5 mb-1">Paginas de contenido</h2>
                        <p class="text-muted small mb-0">Textos reutilizados para contacto y sobre nosotros.</p>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto de introduccion en contacto</label>
                        <textarea name="contact_intro" class="form-control" rows="3">{{ old('contact_intro', $settings['contact_intro']) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo seccion "Sobre nosotros"</label>
                        <input type="text" name="about_heading" class="form-control" value="{{ old('about_heading', $settings['about_heading']) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto principal "Sobre nosotros"</label>
                        <textarea name="about_body" class="form-control" rows="6">{{ old('about_body', $settings['about_body']) }}</textarea>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-main">Guardar ajustes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
