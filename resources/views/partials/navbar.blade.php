@php
    $fvUser = auth()->user();
    $fvInitial = $fvUser
        ? strtoupper(substr($fvUser->nama ?? 'U', 0, 1))
        : 'U';
@endphp

<header class="fv-navbar">

    <div class="d-flex align-items-center gap-2">

        <button
            type="button"
            class="fv-navbar-toggle"
            id="fvSidebarToggle"
            aria-label="Buka menu"
        >
            <i class="bi bi-list"></i>
        </button>

        <span class="fv-navbar-title">
            @yield('title', 'FuelVision')
        </span>

    </div>

    <div class="fv-navbar-user">

        @if ($fvUser)

            <div class="text-end">

                <div class="fv-user-name">
                    {{ $fvUser->nama }}
                </div>

                <div class="fv-user-role">
                    {{ $fvUser->role?->nama_role }}
                </div>

            </div>

            <div class="fv-avatar">
                {{ $fvInitial }}
            </div>

            <form
                method="POST"
                action="{{ route('logout') }}"
                class="mb-0"
            >
                @csrf

                <button
                    type="submit"
                    class="fv-logout-btn"
                    title="Logout"
                >
                    <i class="bi bi-box-arrow-right"></i>
                </button>

            </form>

        @endif

    </div>

</header>