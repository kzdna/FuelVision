@php
    $fvUser = auth()->user();
    $fvRole = $fvUser?->role?->nama_role;

    $fvCanDashboard = in_array($fvRole, ['Admin Finance', 'View Only'], true);
    $fvCanAdminFinanceOnly = $fvRole === 'Admin Finance';
@endphp

<aside
    class="fv-sidebar"
    id="fvSidebar"
>

    <div class="fv-sidebar-brand">

        <div class="fv-sidebar-brand-mark">
            FV
        </div>

        <div class="fv-sidebar-brand-text">
            FuelVision
        </div>

    </div>

    <nav class="fv-sidebar-nav">

        @if ($fvCanDashboard)

            <a
                href="{{ route('dashboard.index') }}"
                class="fv-nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
            >
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="fv-nav-section-label">
                Monitoring
            </div>

            @if ($fvCanAdminFinanceOnly)

                <a
                    href="{{ route('monitoring.mingguan') }}"
                    class="fv-nav-item {{ request()->routeIs('monitoring.mingguan') ? 'active' : '' }}"
                >
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Mingguan</span>
                </a>

                <a
                    href="{{ route('monitoring.bulanan') }}"
                    class="fv-nav-item {{ request()->routeIs('monitoring.bulanan') ? 'active' : '' }}"
                >
                    <i class="bi bi-calendar3"></i>
                    <span>Bulanan</span>
                </a>

            @endif

            <div class="fv-nav-section-label">
                Transaksi
            </div>

            @if ($fvCanAdminFinanceOnly)

                <a
                    href="{{ route('transaksi-pengisian-bbm.index') }}"
                    class="fv-nav-item {{ request()->routeIs('transaksi-pengisian-bbm.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-receipt"></i>
                    <span>Riwayat Transaksi</span>
                </a>

            @endif

            <a
                href="{{ route('monitoring.bulanan.ai-insight') }}"
                class="fv-nav-item {{ request()->routeIs('monitoring.bulanan.ai-insight') ? 'active' : '' }}"
            >
                <i class="bi bi-stars"></i>
                <span>AI Insight</span>
            </a>

            @if ($fvCanAdminFinanceOnly)

                <div class="fv-nav-section-label">
                    Master Data
                </div>

                <a
                    href="{{ route('kendaraan-operasional.index') }}"
                    class="fv-nav-item {{ request()->routeIs('kendaraan-operasional.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-truck"></i>
                    <span>Kendaraan Operasional</span>
                </a>

                <a
                    href="{{ route('kendaraan-gs.index') }}"
                    class="fv-nav-item {{ request()->routeIs('kendaraan-gs.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-truck-flatbed"></i>
                    <span>Kendaraan GS</span>
                </a>

                <a
                    href="{{ route('master-harga-bbm-vendor.index') }}"
                    class="fv-nav-item {{ request()->routeIs('master-harga-bbm-vendor.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-fuel-pump"></i>
                    <span>Harga BBM Vendor</span>
                </a>

                <a
                    href="{{ route('standar-konsumsi-bbm.index') }}"
                    class="fv-nav-item {{ request()->routeIs('standar-konsumsi-bbm.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-speedometer2"></i>
                    <span>Standar Konsumsi BBM</span>
                </a>

            @endif

        @endif

    </nav>

</aside>

<div
    class="fv-sidebar-backdrop"
    id="fvSidebarBackdrop"
></div>