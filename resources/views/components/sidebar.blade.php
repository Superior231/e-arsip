<!-- Sidebar Desktop -->
<nav class="sidebar d-none d-md-flex flex-column p-3" style="width: 250px; min-height: 100vh;">
    <header class="d-flex align-items-center justify-content-between">
        <a href="{{ route('index') }}" class="d-flex align-items-center text-decoration-none">
            <img src="{{ url('/assets/img/logo_ppj.png') }}" alt="logo" style="width: 50px;" class="logo"
                id="logo">
            <span class="nav-name-brand ms-2 fw-semibold text-color fs-5" id="navNameBrand">e-Arsip PPJ</span>
        </a>
        <i class='bx bx-chevron-left toggle' id="sidebarToggle" style="cursor: pointer;"></i>
    </header>

    <div class="menu-bar mt-4">
        <ul class="menu-links">
                <li>
                    <a href="{{ route('index') }}" class="side-link {{ $active === 'dashboard' ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Dashboard">
                        <i class='bx bxs-home icon'></i>
                        <span class="nav-text px-0 mx-0">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('category.index') }}"
                        class="side-link {{ $active === 'category' ? 'active' : '' }}" data-bs-toggle="tooltip"
                        data-bs-placement="right" data-bs-title="Katagori">
                        <i class='bx bxs-purchase-tag icon'></i>
                        <span class="nav-text px-0 mx-0">Kategori</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="side-link {{ $active === 'archive' ? 'active' : '' }}" data-bs-toggle="tooltip"
                        data-bs-placement="right" data-bs-title="Arsip">
                        <i class='bx bxs-archive icon'></i>
                        <span class="nav-text px-0 mx-0">Arsip</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="side-link {{ $active === 'history' ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="History">
                        <i class='bx bx-history icon fs-4'></i>
                        <span class="nav-text px-0 mx-0">History</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="side-link {{ $active === 'staff' ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Staff">
                        <i class='bx bxs-group icon'></i>
                        <span class="nav-text px-0 mx-0">Staff</span>
                    </a>
                </li>
            <li>
                <a href="#" class="side-link {{ $active === 'scan' ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Scan">
                    <i class='bx bx-scan icon'></i>
                    <span class="nav-text px-0 mx-0">Scan</span>
                </a>
            </li>
        </ul>

        <div class="bottom-content position-absolute bottom-0 mb-2" style="width: calc(100% - 40px);">
            <li class="setting">
                <a href="#" class="d-flex align-items-center justify-content-between"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Pengaturan">
                    <div class="d-flex align-items-center">
                        <i class='bx bxs-cog icon'></i>
                        <span class="nav-text">Pengaturan</span>
                    </div>
                    <i class='bx bx-chevron-right nav-text fs-4'></i>
                </a>
            </li>
        </div>
    </div>
</nav>
