@extends('layouts.main')

@section('content')
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0 mb-3">
        <a href="{{ route('pengaturan.index') }}" class="breadcrumb-items">Pengaturan</a>
        <a href="" class="breadcrumb-items active">Profile</a>
    </ol>
    <!-- Breadcrumb End -->

    <div class="container-edit-akun pb-5">
        <div class="row row-cols-1 row-cols-lg-2 pb-5 g-3">
            <div class="col col-lg-4">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-4 py-4">
                        <figure class="profile d-flex flex-column justify-content-center align-items-center gap-3">
                            <div class="foto-profile">
                                @if (!empty(Auth::user()->avatar))
                                    <img class="img" src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}">
                                @else
                                    <img class="img"
                                        src="https://ui-avatars.com/api/?background=random&name={{ urlencode(Auth::user()->name) }}">
                                @endif
                            </div>
                            <figcaption class="text-color text-center fw-semibold w-75">{{ Auth::user()->name }}
                            </figcaption>
                        </figure>

                        <div class="profile-details table-responsive">
                            <table class="table">
                                <tr>
                                    <td>Username</td>
                                    <td>:</td>
                                    <td>{{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <td>Role</td>
                                    <td>:</td>
                                    <td>{{ Auth::user()->roles }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col col-lg-8 d-flex flex-column gap-2">
                <div class="edit-profile py-4" data-bs-toggle="modal" data-bs-target="#edit-profile">
                    <a href="#" class="row d-flex justify-content-between text-decoration-none">
                        <div class="col d-flex align-items-center gap-3">
                            <i class='bx bx-user text-color fs-2 icon'></i>
                            <h5 class="text-color py-0 my-0">Edit profile</h5>
                        </div>
                    </a>
                </div>

                <a id="logout-confirmation" href="{{ route('logout') }}" class="logout text-decoration-none py-4"
                    onclick="event.preventDefault(); logout();">
                    <div class="row d-flex justify-content-between">
                        <div class="col d-flex align-items-center gap-3">
                            <i class='bx bx-arrow-from-left text-color fs-2 icon'></i>
                            <h5 class="text-color py-0 my-0 fw-bold">Logout</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="edit-profile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-body-dark">
            <div class="modal-content">
                <div class="modal-header border-0 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title" id="edit-profile-label">Edit profile</h5>
                    <div class="close-btn" data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;">
                        <i class='bx bx-x text-color fs-2 icon'></i>
                    </div>
                </div>
                <div class="reset-btn">
                    <form id="reset-avatar-form-{{ $user->id }}"
                        action="{{ route('profile.delete.avatar', $user->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button" onclick="resetAvatar({{ $user->id }})">Delete</button>
                    </form>
                </div>

                <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="modal-body">
                        <figure class="profile d-flex flex-column justify-content-center align-items-center gap-3 mb-4">
                            <div class="foto-profile">
                                @if (!empty(Auth::user()->avatar))
                                    <img class="img-avatar" id="img"
                                        src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}">
                                @else
                                    <img class="img-avatar" id="img"
                                        src="https://ui-avatars.com/api/?background=random&name={{ urlencode(Auth::user()->name) }}">
                                @endif
                            </div>
                        </figure>
                        <label for="upload-foto" class="mb-2">Upload foto (jpg, jpeg, png, dan webp)</label>
                        <input type="file" class="form-control" name="avatar" id="input-img" accept="image/*">

                        <label for="edit-username" class="mb-2 mt-3">Username</label>
                        <input type="text" class="form-control" name="name" id="edit-username"
                            placeholder="Enter username" value="{{ Auth::user()->name }}" autocomplete="off" required>

                        <label for="edit-password" class="mb-2 mt-3">Password Baru</label>
                        <input type="password" class="form-control" name="password" id="edit-password"
                            placeholder="Enter new password" autocomplete="off">
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4" id="save-edit-profile-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal End -->
@endsection


@push('scripts')
    <script>
        function resetAvatar(userId) {
            Swal.fire({
                icon: 'question',
                title: 'Anda Yakin?',
                text: 'Apakah Anda yakin ingin menghapus avatar ini?',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                customClass: {
                    popup: 'bg-modal',
                    title: 'text-color',
                    htmlContainer: 'text-color fw-normal',
                    closeButton: 'bg-secondary border-0 shadow-none',
                    confirmButton: 'bg-danger border-0 shadow-none',
                },
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reset-avatar-form-' + userId).submit();
                }
            });
        }
    </script>
@endpush
