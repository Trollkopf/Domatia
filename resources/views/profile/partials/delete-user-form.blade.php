<section class="profile-card profile-danger">
    <header class="mb-4">
        <h2>{{ __('frontend.profile.danger_title') }}</h2>
        <p class="profile-card-intro mb-0">{{ __('frontend.profile.danger_intro') }}</p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        <div class="mb-3">
            <label for="delete_password" class="form-label">{{ __('frontend.profile.delete_password') }}</label>
            <input
                id="delete_password"
                name="password"
                type="password"
                class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                placeholder="{{ __('frontend.profile.password_placeholder') }}"
            >
            @if ($errors->userDeletion->has('password'))
                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <p class="text-muted mb-0">{{ __('frontend.profile.delete_warning') }}</p>
            <button type="submit" class="btn btn-outline-danger">{{ __('frontend.profile.delete_account') }}</button>
        </div>
    </form>
</section>
