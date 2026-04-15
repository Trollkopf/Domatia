<section class="profile-card">
    <header class="mb-4">
        <h2>Seguridad de la cuenta</h2>
        <p class="profile-card-intro">Cambia la contrasena por una mas robusta para mantener el acceso bajo control.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="row g-3">
            <div class="col-12">
                <label for="current_password" class="form-label">Contrasena actual</label>
                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                    autocomplete="current-password"
                >
                @if ($errors->updatePassword->has('current_password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="password" class="form-label">Nueva contrasena</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                    autocomplete="new-password"
                >
                @if ($errors->updatePassword->has('password'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirmar nueva contrasena</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                    autocomplete="new-password"
                >
                @if ($errors->updatePassword->has('password_confirmation'))
                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap mt-4">
            <button type="submit" class="btn btn-dark">Actualizar contrasena</button>

            @if (session('status') === 'password-updated')
                <span class="profile-feedback">Contrasena actualizada correctamente</span>
            @endif
        </div>
    </form>
</section>
