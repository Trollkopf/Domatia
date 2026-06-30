<section class="profile-card">
    <header class="mb-4">
        <h2>{{ __('frontend.profile.access_title') }}</h2>
        <p class="profile-card-intro">{{ __('frontend.profile.access_intro') }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-none">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">{{ __('frontend.profile.full_name') }}</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                    autocomplete="name"
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">{{ __('frontend.profile.email') }}</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning rounded-4 border-0 mt-4 mb-0">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <div>
                        <strong>{{ __('frontend.profile.email_unverified') }}</strong>
                        <div class="small mt-1">{{ __('frontend.profile.email_verify_help') }}</div>
                    </div>

                    <button form="send-verification" class="btn btn-outline-dark btn-sm" type="submit">
                        {{ __('frontend.profile.resend_link') }}
                    </button>
                </div>
            </div>
        @endif

        <div class="d-flex align-items-center gap-3 flex-wrap mt-4">
            <button type="submit" class="btn btn-dark">{{ __('frontend.profile.save_changes') }}</button>

            @if (session('status') === 'profile-updated')
                <span class="profile-feedback">{{ __('frontend.profile.profile_updated') }}</span>
            @endif
        </div>
    </form>
</section>
