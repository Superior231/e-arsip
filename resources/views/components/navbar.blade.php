<nav class="navbar shadow-sm">
    <div class="container-fluid">
        <div class="navbar-brand d-flex align-items-center gap-3">
            <i class='bx bx-menu d-md-none fs-1 mt-1' type="button" data-bs-toggle="offcanvas"
                data-bs-target="#mobileNav" aria-controls="mobileNav"></i>
            <span class="fw-semibold my-0 py-0">{{ $navTitle }}</span>
        </div>
        <ul class="navbar-nav me-2 me-md-3 d-flex flex-row align-items-center gap-4" id="dropdown">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                    id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-image">
                        @if (!empty(Auth::user()->avatar))
                            <img class="img" src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}">
                        @else
                            <img class="img"
                                src="https://ui-avatars.com/api/?background=random&name={{ urlencode(Auth::user()->name) }}">
                        @endif
                    </div>
                    <span class="nav-username">&nbsp;{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end position-absolute" aria-labelledby="navbarDropdownMenuLink">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                            <i class='bx bx-user'></i>
                            Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2"
                            href="#">
                            <i class='bx bx-cog'></i>
                            Pengaturan
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider py-0 my-1">
                    </li>
                    <li>
                        <a id="logout-confirmaton" class="dropdown-item d-flex align-items-center gap-2"
                            href="{{ route('logout') }}" onclick="event.preventDefault(); logout();">
                            <i class='bx bx-log-in'></i>
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <div class="d-flex align-items-center">
            <a href="{{ route('index') }}">
                <img src="{{ url('/assets/img/logo_ppj.png') }}" alt="logo" style="width: 40px;"
                    class="logo" id="logo">
            </a>
            <span class="nav-name-brand ms-2 fw-semibold text-color" id="navNameBrand">e-Arsip PPJ</span>
        </div>
        <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>

    <hr class="border-secondary py-0 my-0">

    <div class="offcanvas-body mx-0 px-0">
        <ul class="list-unstyled">
            <li class="{{ $active == 'dashboard' ? 'active' : '' }}">
                <a href="{{ route('index') }}" class="d-flex align-items-center gap-2">
                    <i class='bx bxs-home fs-4'></i>
                    <span class="my-0 py-0">Dashboard</span>
                </a>
            </li>
            <li class="{{ $active == 'category' ? 'active' : '' }}">
                <a href="{{ route('category.index') }}" class="d-flex align-items-center gap-2">
                    <i class='bx bxs-purchase-tag fs-4'></i>
                    <span class="my-0 py-0">Kategori</span>
                </a>
            </li>
            <li class="{{ $active == 'archive' ? 'active' : '' }}">
                <a href="#" class="d-flex align-items-center gap-2">
                    <i class='bx bxs-archive fs-4'></i>
                    <span class="my-0 py-0">Arsip</span>
                </a>
            </li>
            <li class="{{ $active == 'history' ? 'active' : '' }}">
                <a href="#" class="d-flex align-items-center gap-2">
                    <i class='bx bx-history fs-3'></i>
                    <span class="my-0 py-0">History</span>
                </a>
            </li>
            <li class="{{ $active == 'staff' ? 'active' : '' }}">
                <a href="#" class="d-flex align-items-center gap-2">
                    <i class='bx bxs-group fs-4'></i>
                    <span class="my-0 py-0">Staff</span>
                </a>
            </li>
            <li class="{{ $active == 'scan' ? 'active' : '' }}">
                <a href="#" class="d-flex align-items-center gap-2">
                    <i class='bx bx-scan fs-4'></i>
                    <span class="my-0 py-0">Scan</span>
                </a>
            </li>

            <div class="bottom-content position-absolute bottom-0 mb-2" style="width: calc(100% - 1px);">
                <hr class="border-secondary pb-1 mb-1">
                <li class="{{ $active == 'pengaturan' ? 'active' : '' }}">
                    <a href="#" class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class='bx bxs-cog fs-4'></i>
                            <span class="my-0 py-0">Pengaturan</span>
                        </div>
                        <i class='bx bx-chevron-right fs-3'></i>
                    </a>
                </li>
            </div>
        </ul>
    </div>
</div>


@push('scripts')
    <script>
        function logout() {
            Swal.fire({
                icon: 'question',
                title: 'Anda Yakin?',
                text: 'Apakah Anda yakin ingin logout?',
                showCancelButton: true,
                confirmButtonText: 'Logout',
                customClass: {
                    popup: 'sw-popup',
                    title: 'sw-title',
                    htmlContainer: 'sw-text',
                    icon: 'sw-icon',
                    closeButton: 'bg-secondary border-0 shadow-none',
                    confirmButton: 'bg-danger border-0 shadow-none',
                },
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
@endpush
