@php
    use App\Models\Letter;
    $letter_pending = Letter::where('status', 'pending')->count();
@endphp

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
            @if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'superadmin')
                <li>
                    <a href="{{ route('division.index') }}"
                        class="side-link {{ $active === 'division' ? 'active' : '' }}" data-bs-toggle="tooltip"
                        data-bs-placement="right" data-bs-title="Divisi">
                        <i class='bx bxs-label icon'></i>
                        <span class="nav-text px-0 mx-0">Divisi</span>
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
            @endif
            <li>
                <a href="#" class="side-link justify-content-between {{ $active === 'archive' ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Arsip" onclick="toggleArchive()">
                    <div class="d-flex align-items-center">
                        <i class='bx bxs-archive icon'></i>
                        <span class="nav-text px-0 mx-0">Arsip</span>
                    </div>
                    <i class='bx bx-chevron-right nav-text fs-5 me-3' id="archiveIcon"></i>
                </a>
            </li>
            <li class="archive-menu d-none d-flex flex-column gap-2 mb-3 ms-3" style="height: 100%;">
                <a href="{{ route('archive.index') }}" class="side-link text-secondary">💠 Semua</a>
                <a href="{{ route('letter.index') }}" class="side-link text-secondary">💠 Surat</a>
                <a href="{{ route('letterIn.index') }}" class="side-link text-secondary">💠 Surat Masuk</a>
                <a href="{{ route('letterOut.index') }}" class="side-link text-secondary">💠 Surat Keluar</a>
                <a href="{{ route('faktur.index') }}" class="side-link text-secondary">💠 Faktur</a>
                <a href="{{ route('memo.index') }}" class="side-link text-secondary">💠 Memo</a>
            </li>
            <li>
                <a href="{{ route('history.index') }}" class="side-link {{ $active === 'history' ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="History">
                    <i class='bx bx-history icon fs-4'></i>
                    <span class="nav-text px-0 mx-0">History</span>
                </a>
            </li>
            @if (Auth::user()->roles === 'superadmin')
                <li>
                    <a href="{{ route('letter.pending') }}"
                        class="side-link d-flex position-relative justify-content-between {{ $active === 'pending' ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Pending">
                        <div class="d-flex align-items-center gap-0">
                            <i class='bx bxs-time-five icon fs-5'></i>
                            <span class="nav-text px-0 mx-0">Pending</span>
                        </div>
                        @if ($letter_pending > 0)
                            <span class="position-absolute badge rounded-pill bg-danger mt-3"
                                style="z-index: 99999; right: 0; bottom: 25px;">
                                {{ $letter_pending }}
                            </span>
                        @endif
                    </a>
                </li>
            @endif
            @if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'superadmin')
                <li>
                    <a href="{{ route('staff.index') }}" class="side-link {{ $active === 'staff' ? 'active' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Staff">
                        <i class='bx bxs-group icon'></i>
                        <span class="nav-text px-0 mx-0">Staff</span>
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('scan.index') }}" class="side-link {{ $active === 'scan' ? 'active' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Scan">
                    <i class='bx bx-scan icon'></i>
                    <span class="nav-text px-0 mx-0">Scan</span>
                </a>
            </li>
        </ul>

        <div class="bottom-content position-absolute bottom-0 mb-2" style="width: calc(100% - 40px);">
            <li class="setting">
                <a href="{{ route('pengaturan.index') }}" class="d-flex align-items-center justify-content-between"
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
