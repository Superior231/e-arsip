@extends('layouts.main')

@push('styles')
    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .username .username-info {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .username .username-info h5 {
            margin-left: 40px;
        }

        .username .username-info .avatar {
            position: absolute;
            width: 30px;
            height: 30px;
            overflow: hidden;
            border-radius: 50%;
        }

        .username .username-info .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .history-icon {
            position: fixed;
            top: 70px;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: end;
            cursor: pointer;
            z-index: 9;
        }

        .history-icon>* {
            background-color: #fa967d;
            color: #fff;
            padding: 5px 10px;
            border-radius: 30px 0px 0px 30px;
        }
    </style>
@endpush

@section('content')
    <div class="history-icon mb-2 d-none" onclick="historyStaff()">
        <div class="d-flex align-items-center">
            <i class='bx bx-chevrons-right fs-3' id="iconHistory"></i>
            <span class="my-0 py-0">History</span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 staff-container">
        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-group fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">Total Users</h4>
                            <span class="py-0 my-0">{{ $users->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-check-circle fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">Approved Users</h4>
                            <span class="py-0 my-0">{{ $approved_users->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-shield-x fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">Suspended Users</h4>
                            <span class="py-0 my-0">{{ $suspended_users->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-crown fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">Super Admin</h4>
                            <span class="py-0 my-0">{{ $superadmin->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-wrench fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">Admin</h4>
                            <span class="py-0 my-0">{{ $admin->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-group fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">Users</h4>
                            <span class="py-0 my-0">{{ $staff->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-lg-2 g-3 mt-0 mt-lg-4" id="staffListContainer">
        <div class="col col-12 col-lg-8" id="staffList">
            <div class="card p-4 pt-3">
                <div class="actions d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold py-0 my-0">Semua Staff</h4>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal"
                        data-bs-target="#tambahSaffModal">
                        <i class='bx bx-plus'></i>
                        Staff
                    </button>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-striped" id="staffTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="align-middle">
                                    <td>
                                        <div class="username">
                                            <div class="username-info">
                                                <div class="avatar">
                                                    @if (!empty($user->avatar))
                                                        <img class="img"
                                                            src="{{ asset('storage/avatars/' . $user->avatar) }}">
                                                    @else
                                                        <img class="img"
                                                            src="https://ui-avatars.com/api/?background=random&name={{ urlencode($user->name) }}">
                                                    @endif
                                                </div>
                                                <h5 class="py-0 my-0">{{ $user->name }}</h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-nowrap">
                                            <i
                                                class='bx
                                            {{ $user->roles === 'superadmin'
                                                ? 'bxs-crown text-warning'
                                                : ($user->roles === 'admin'
                                                    ? 'bxs-wrench'
                                                    : 'bxs-user') }}
                                            fs-5'></i>
                                            {{ $user->roles }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status d-flex justify-content-center pe-3">
                                            @if ($user->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Suspended</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions d-flex align-items-center justify-content-center gap-2 pe-3">
                                            @if (Auth::user()->roles === 'superadmin')
                                                <div style="cursor: pointer;"
                                                    class="btn btn-primary d-flex align-items-center justify-content-center p-2"
                                                    onclick="editStaff('{{ $user->id }}', '{{ $user->avatar }}', '{{ $user->name }}', '{{ $user->roles }}', '{{ $user->status }}')"
                                                    data-bs-toggle="modal" data-bs-target="#editStaffModal">
                                                    <i class='bx bxs-pencil p-0 m-0'></i>
                                                </div>
                                            @elseif (Auth::user()->roles === 'admin' &&
                                                    $user->roles !== 'superadmin' &&
                                                    $user->roles !== 'admin' &&
                                                    Auth::user()->id !== $user->id)
                                                <div style="cursor: pointer;"
                                                    class="btn btn-primary d-flex align-items-center justify-content-center p-2"
                                                    onclick="editStaff('{{ $user->id }}', '{{ $user->avatar }}', '{{ $user->name }}', '{{ $user->roles }}', '{{ $user->status }}')"
                                                    data-bs-toggle="modal" data-bs-target="#editStaffModal">
                                                    <i class='bx bxs-pencil p-0 m-0'></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col col-12 col-lg-4" id="historyStaff">
            <div class="card">
                <div class="card-body px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-semibold my-0 py-0 px-3">Riwayat Staff</h4>
                        <i class='bx bx-chevrons-right fs-3 px-3' id="iconHistory" onclick="historyStaff()"
                            style="cursor: pointer;"></i>
                    </div>
                    <hr class="pb-0 mb-0">
                    <div class="d-flex flex-column" style="max-height: 500px; overflow-y: scroll">
                        @forelse ($history_staff as $history)
                            <a href="{{ route('history.detail', $history->id) }}" class="text-decoration-none text-dark"
                                style="cursor: pointer;">
                                @php
                                    $bgClass = match ($history->method) {
                                        'create' => 'bg-success text-light',
                                        'update' => 'bg-warning',
                                        'update status' => 'bg-primary text-light',
                                        'update status, update' => 'bg-dark text-light',
                                        'delete' => 'bg-danger text-light',
                                        default => 'bg-secondary text-dark',
                                    };
                                @endphp

                                <div class="notification-header d-flex justify-content-between {{ $bgClass }} px-3">
                                    <span class="fs-7">{{ $history->title }}</span>
                                    <span
                                        class="fs-7">{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i') }}
                                        WIB</span>
                                </div>
                                <div class="notification-body d-flex flex-column px-3 gap-0 mb-2">
                                    <p class="my-0 py-0 fs-6 fw-bold">{{ $history->name }}</p>
                                    <p class="my-0 py-0 fs-7">{{ $history->description }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="px-3 pt-2 d-flex justify-content-center align-items-center">
                                <span>Belum ada riwayat.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="tambahSaffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center justify-content-between">
                        <h4 class="modal-title" id="tambahStaffLabel">Tambah Staff</h4>
                        <div class="close-btn" data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;">
                            <i class='bx bx-x fs-2 icon'></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        @if (Auth::user()->roles == 'superadmin')
                            <label for="roles-tambah" class="mb-2">Roles</label>
                            <select id="roles-tambah" name="roles" class="form-select mb-3"
                                aria-label="Default select example">
                                <option value="user">user</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        @endif

                        <label for="nameTambah" class="mb-2">Username</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" name="name"
                            id="nameTambah" value="{{ old('name') }}" placeholder="Enter username" autocomplete="off"
                            required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="error-message-container mb-2 mt-3">
                            <label for="password-tambah">Password</label>
                            <p class="text-danger fw-bolder py-0 my-0" id="error-message-tambah-password"></p>
                        </div>
                        <div class="content-tambah-user" id="content-tambah-password">
                            <input type="password" class="form-control" name="password" id="password-tambah"
                                placeholder="Enter password" autocomplete="off" required>
                            <div class="pass-logo-pass" style="background-color: transparent;">
                                <div class="showPass" id="showPassTambah" style="display: none;"><i
                                        class="fa-regular fa-eye-slash"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-body-dark">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title" id="editStaffLabel">Edit Staff</h4>
                    <div class="close-btn" data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;">
                        <i class='bx bx-x text-color fs-2 icon'></i>
                    </div>
                </div>

                <form action="" method="POST" enctype="multipart/form-data" id="editStaffForm">
                    @csrf @method('PUT')

                    <div class="modal-body">
                        <figure class="profile d-flex flex-column justify-content-center align-items-center gap-3 mb-4">
                            <div class="foto-profile">
                                <img class="img-avatar" id="img">
                            </div>
                        </figure>
                        <label for="upload-foto" class="mb-2">Upload foto (jpg, jpeg, png, dan webp)</label>
                        <input type="file" class="form-control" name="avatar" id="input-img" accept="image/*">

                        @if (Auth::user()->roles == 'superadmin')
                            <div class="d-flex align-items-center gap-2 w-100">
                                <div class="mt-3 w-100">
                                    <label for="edit-roles" class="mb-2">Role</label>
                                    <select class="form-select" name="roles" id="edit-roles" required>
                                        <option value="admin">Admin</option>
                                        <option value="superadmin">Superadmin</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>
                                <div class="mt-3 w-100">
                                    <label for="edit-status" class="mb-2">Status</label>
                                    <select class="form-select" name="status" id="edit-status" required>
                                        <option value="approved">Approved</option>
                                        <option value="suspend">Suspend</option>
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 w-100">
                                <label for="edit-status" class="mb-2">Status</label>
                                <select class="form-select" name="status" id="edit-status" required>
                                    <option value="approved">Approved</option>
                                    <option value="suspend">Suspend</option>
                                </select>
                            </div>
                        @endif

                        <label for="edit-username" class="mb-2 mt-3">Username</label>
                        <input type="text" class="form-control text-color" name="name" id="edit-username"
                            placeholder="Enter username" autocomplete="off" required>

                        <div class="mt-3">
                            <label for="edit-password" class="mb-2">Password Baru</label>
                            <input type="password" class="form-control text-color" name="password" id="edit-password"
                                placeholder="Enter new password" autocomplete="off">
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4" id="save-edit-profile-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Datatables Js -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#staffTable').DataTable({
                "language": {
                    "searchPlaceholder": "Search..."
                }
            });
        });

        function editStaff(id, avatar, name, roles, status) {
            var avatarUrl = avatar ? '{{ asset('storage/avatars') }}/' + avatar :
                `https://ui-avatars.com/api/?background=random&name=${encodeURIComponent(name)}`;

            $('#img').attr('src', avatarUrl);
            $('#input-img').val('');
            $('#edit-username').val(name);
            $('#edit-roles').val(roles);
            $('#edit-status').val(status);

            $('#editStaffForm').attr('action', "{{ route('staff.update', '') }}" + '/' + id);
        }

        function historyStaff() {
            let historyDiv = document.getElementById('historyStaff');
            let staffList = document.getElementById('staffList');
            let staffListContainer = document.getElementById('staffListContainer');
            let historyIcon = document.querySelector('.history-icon');
            let icon = document.getElementById("iconHistory");

            if (icon.classList.contains("bx-chevrons-right")) {
                icon.classList.remove("bx-chevrons-right");
                icon.classList.add("bx-chevrons-left");
                localStorage.setItem("historyStaffState", "closed");
            } else {
                icon.classList.remove("bx-chevrons-left");
                icon.classList.add("bx-chevrons-right");
                localStorage.setItem("historyStaffState", "open");
            }

            if (historyDiv.style.display === 'none') {
                historyDiv.style.display = 'block';
                staffList.classList.remove('col-lg-12');
                staffList.classList.add('col-lg-8');
                staffListContainer.classList.remove('row-cols-lg-1', 'mt-4');
                staffListContainer.classList.add('row-cols-lg-2', 'mt-0');
                historyIcon.classList.remove('d-flex');
                historyIcon.classList.add('d-none');
            } else {
                historyDiv.style.display = 'none';
                staffList.classList.remove('col-lg-8');
                staffList.classList.add('col-lg-12');
                staffListContainer.classList.remove('row-cols-lg-2', 'mt-0');
                staffListContainer.classList.add('row-cols-lg-1', 'mt-4');
                historyIcon.classList.remove('d-none');
                historyIcon.classList.add('d-flex');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const state = localStorage.getItem("historyStaffState");
            if (state === "closed") {
                historyStaff();
            }
        });
    </script>
@endpush
