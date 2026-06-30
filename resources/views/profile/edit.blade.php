@extends(auth()->user()?->canAccessBackoffice() ? 'layouts.admin' : 'layouts.guest')

@section('title', __('frontend.profile_page.title'))
@section('meta_title', __('frontend.profile_page.title'))
@section('meta_description', __('frontend.profile_page.intro'))
@section('meta_robots', 'noindex,follow')

@section('styles')
    <style>
        .profile-shell {
            background:
                radial-gradient(circle at top left, rgba(184, 138, 59, 0.12), transparent 24%),
                linear-gradient(180deg, #f8fafc 0%, #ffffff 42%);
            min-height: 100%;
        }

        .profile-hero,
        .profile-card {
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }

        .profile-hero {
            padding: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .profile-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.85rem;
            border-radius: 999px;
            background: #f8fafc;
            color: #8b6a33;
            border: 1px solid rgba(184, 138, 59, 0.2);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .profile-hero h1 {
            margin: 1rem 0 0.65rem;
            font-size: clamp(1.9rem, 3vw, 2.8rem);
            letter-spacing: -0.04em;
            color: #182230;
        }

        .profile-hero p {
            color: #5b6777;
            max-width: 60ch;
            margin: 0;
        }

        .profile-card {
            padding: 1.4rem;
        }

        .profile-card + .profile-card {
            margin-top: 1rem;
        }

        .profile-summary-card {
            position: sticky;
            top: 1.5rem;
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #111827 0%, #374151 100%);
            color: #fff;
            font-size: 1.6rem;
            font-weight: 700;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.16);
        }

        .profile-summary-name {
            margin: 1rem 0 0.25rem;
            font-size: 1.3rem;
            color: #182230;
            font-weight: 700;
        }

        .profile-summary-meta {
            color: #64748b;
            font-size: 0.95rem;
        }

        .profile-stat-list {
            display: grid;
            gap: 0.8rem;
            margin-top: 1.25rem;
        }

        .profile-stat {
            padding: 0.9rem 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .profile-stat-label {
            display: block;
            margin-bottom: 0.2rem;
            color: #64748b;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .profile-stat-value {
            color: #182230;
            font-weight: 600;
        }

        .profile-actions {
            display: grid;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }

        .profile-card h2 {
            color: #182230;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .profile-card .profile-card-intro {
            color: #64748b;
            margin-bottom: 1.25rem;
        }

        .profile-card .form-control {
            min-height: 2.85rem;
            border-radius: 16px;
            border-color: #d8dee8;
            box-shadow: none;
        }

        .profile-card .form-control:focus {
            border-color: rgba(184, 138, 59, 0.6);
            box-shadow: 0 0 0 0.2rem rgba(184, 138, 59, 0.14);
        }

        .profile-card .form-label {
            font-weight: 600;
            color: #334155;
        }

        .profile-danger {
            border-color: rgba(220, 38, 38, 0.15);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(254, 242, 242, 0.9));
        }

        .profile-danger h2 {
            color: #991b1b;
        }

        .profile-feedback {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.8rem;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 0.9rem;
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .profile-summary-card {
                position: static;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $user = $user ?? auth()->user();
        $hasBackofficeAccess = $user?->canAccessBackoffice();
        $canManageSettings = $user?->canManageSettings();
    @endphp

    <section class="profile-shell py-4 py-lg-5">
        <div class="container-fluid px-0">
            <div class="profile-hero">
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">
                    <div>
                        <span class="profile-kicker">{{ __('frontend.profile_page.kicker') }}</span>
                        <h1>{{ __('frontend.profile_page.title') }}</h1>
                        <p>{{ __('frontend.profile_page.intro') }}</p>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        @if ($hasBackofficeAccess)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark">{{ __('frontend.profile_page.back_to_backoffice') }}</a>
                        @else
                            <a href="{{ url('/') }}" class="btn btn-outline-dark">{{ __('frontend.profile_page.back_home') }}</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-4">
                    <aside class="profile-card profile-summary-card">
                        <div class="profile-avatar">
                            {{ \Illuminate\Support\Str::of($user->name)->trim()->substr(0, 1)->upper() }}
                        </div>

                        <div class="profile-summary-name">{{ $user->name }}</div>
                        <div class="profile-summary-meta">{{ $user->email }}</div>

                        <div class="profile-stat-list">
                            <div class="profile-stat">
                                <span class="profile-stat-label">{{ __('frontend.profile_page.role') }}</span>
                                <div class="profile-stat-value">{{ $user->groupLabel() }}</div>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-label">{{ __('frontend.profile_page.email_status') }}</span>
                                <div class="profile-stat-value">{{ $user->email_verified_at ? __('frontend.profile_page.verified') : __('frontend.profile_page.verification_pending') }}</div>
                            </div>
                            <div class="profile-stat">
                                <span class="profile-stat-label">{{ __('frontend.profile_page.member_since') }}</span>
                                <div class="profile-stat-value">{{ optional($user->created_at)->format('d/m/Y') ?: __('frontend.profile_page.no_date') }}</div>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <a href="{{ route('profile.edit') }}" class="btn btn-dark">{{ __('frontend.profile_page.manage_account') }}</a>
                            @if ($canManageSettings)
                                <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">{{ __('frontend.profile_page.go_to_settings') }}</a>
                            @endif
                        </div>
                    </aside>
                </div>

                <div class="col-xl-8">
                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ __('frontend.profile_page.verification_sent') }}</div>
                    @endif

                    @include('profile.partials.update-profile-information-form')
                    @include('profile.partials.update-password-form')
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </section>
@endsection
