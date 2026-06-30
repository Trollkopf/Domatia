@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre completo *</label>
        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $propietario->nombre ?? '') }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $propietario->telefono ?? '') }}">
        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $propietario->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notas" class="form-label">Notas internas</label>
        <textarea id="notas" name="notas" class="form-control @error('notas') is-invalid @enderror" rows="5" placeholder="Preferencias de contacto, disponibilidad, observaciones…">{{ old('notas', $propietario->notas ?? '') }}</textarea>
        @error('notas')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
