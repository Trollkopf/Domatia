<section class="profile-card profile-danger">
    <header class="mb-4">
        <h2>Zona delicada</h2>
        <p class="profile-card-intro mb-0">Si eliminas la cuenta, el acceso desaparecera de forma permanente. Hazlo solo si estas completamente seguro.</p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        <div class="mb-3">
            <label for="delete_password" class="form-label">Contrasena actual para confirmar</label>
            <input
                id="delete_password"
                name="password"
                type="password"
                class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                placeholder="Introduce tu contrasena"
            >
            @if ($errors->userDeletion->has('password'))
                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <p class="text-muted mb-0">La eliminacion es irreversible y borra los datos asociados a la cuenta.</p>
            <button type="submit" class="btn btn-outline-danger">Eliminar cuenta</button>
        </div>
    </form>
</section>
