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
    </style>
@endpush

@section('content')
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

    <div class="staff mt-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="fw-semibold py-0 my-0">Semua Staff</h3>
            <a href="#" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal"
                data-bs-target="#tambahSaffModal">
                <i class='bx bx-plus'></i>
                Tambah Staff
            </a>
        </div>

        <div class="table-responsive card p-4">
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
                                                <img class="img" src="{{ asset('storage/avatars/' . $user->avatar) }}">
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
                                    <i class='bx
                                    {{ $user->roles === 'superadmin' ? 'bxs-crown text-warning' : 
                                    ($user->roles === 'admin' ? 'bxs-wrench' : 'bxs-user') }}
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
    </script>
@endpush
