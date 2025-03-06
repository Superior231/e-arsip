@extends('layouts.main')

@push('styles')
    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
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
    <div class="history-icon mb-2 d-none" onclick="historyDivision()">
        <div class="d-flex align-items-center">
            <i class='bx bx-chevrons-right fs-3' id="iconHistory"></i>
            <span class="my-0 py-0">History</span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-lg-2 g-3 mt-0" id="divisionContainer">
        <div class="col col-12 col-lg-8" id="divisionList">
            <div class="card p-4 pt-3">
                <div class="actions d-flex align-items-center justify-content-between">
                    <h4 class="fw-semibold py-0 my-0">Daftar Divisi</h4>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal"
                        data-bs-target="#createDivisionModal">
                        <i class='bx bx-plus'></i>
                        Divisi
                    </button>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="myDataTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tempat</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($divisions as $division)
                                <tr class="align-middle">
                                    <td>
                                        <span>{{ $division->name }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $division->place }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center pe-3">
                                            <span
                                                class="badge {{ $division->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $division->status }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-none">{{ $division->created_at }}</span>
                                        <div class="d-flex justify-content-center align-items-center pe-3">
                                            <span>{{ $division->created_at->format('d M Y H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions d-flex justify-content-center pe-3 gap-2">
                                            <button type="button" class="btn btn-primary d-flex align-items-center p-2"
                                                data-bs-toggle="modal" data-bs-target="#editDivisionModal"
                                                onclick="editDivision('{{ $division->id }}', '{{ $division->status }}', '{{ $division->name }}', '{{ $division->place }}')">
                                                <i class='bx bx-pencil p-0 m-0'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col col-12 col-lg-4" id="historyDivision">
            <div class="card">
                <div class="card-body px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-semibold my-0 py-0 px-3">Riwayat Divisi</h4>
                        <i class='bx bx-chevrons-right fs-3 px-3' id="iconHistory" onclick="historyDivision()"
                            style="cursor: pointer;"></i>
                    </div>
                    <hr class="pb-0 mb-0">
                    <div class="d-flex flex-column" style="max-height: 500px; overflow-y: scroll">
                        @forelse ($history_division as $history)
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
                                <span class="fs-7">{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i') }}
                                    WIB</span>
                            </div>
                            <div class="notification-body d-flex flex-column px-3 gap-0 mb-2">
                                <p class="my-0 py-0 fs-6 fw-bold">{{ $history->name }}</p>
                                <p class="my-0 py-0 fs-7">{{ $history->description }}</p>
                            </div>
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
    <div class="modal fade" id="createDivisionModal" tabindex="-1" aria-labelledby="createDivisionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('division.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="createDivisionModalLabel">Buat Divisi</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama divisi</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-label'></i>
                                </span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" aria-describedby="name" value="{{ old('name') }}"
                                    placeholder="Masukkan nama divisi">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="place" class="form-label">Tempat divisi</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-buildings'></i>
                                </span>
                                <input type="text" class="form-control @error('place') is-invalid @enderror"
                                    id="place" name="place" aria-describedby="place" value="{{ old('place') }}"
                                    placeholder="Masukkan tempat divisi">
                                @error('place')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editDivisionModal" tabindex="-1" aria-labelledby="editDivisionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editDivisionForm" method="POST">
                    @csrf @method('PUT')

                    <div class="modal-header">
                        <h4 class="modal-title" id="editDivisionModalLabel">Edit Divisi</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editDivisionStatus" class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-loader bx-rotate'></i>
                                </span>
                                <select class="form-select @error('status') is-invalid @enderror" id="editDivisionStatus"
                                    name="status">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDivisionName" class="form-label">Nama divisi</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-label'></i>
                                </span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="editDivisionName" name="name" aria-describedby="name"
                                    value="{{ old('name') }}" placeholder="Masukkan nama divisi">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDivisionPlace" class="form-label">Tempat divisi</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-buildings'></i>
                                </span>
                                <input type="text" class="form-control @error('place') is-invalid @enderror"
                                    id="editDivisionPlace" name="place" aria-describedby="place"
                                    value="{{ old('place') }}" placeholder="Masukkan tempat divisi">
                                @error('place')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
            $('#myDataTable').DataTable({
                "language": {
                    "searchPlaceholder": "Search..."
                }
            });
        });

        function editDivision(id, status, name, place) {
            $('#editDivisionStatus').val(status);
            $('#editDivisionName').val(name);
            $('#editDivisionPlace').val(place);

            $('#editDivisionForm').attr('action', "{{ route('division.update', '') }}" + '/' + id);
            $('#editDivisionModal').modal('show');
        }

        function historyDivision() {
            let historyDiv = document.getElementById('historyDivision');
            let divisionList = document.getElementById('divisionList');
            let divisionContainer = document.getElementById('divisionContainer');
            let historyIcon = document.querySelector('.history-icon');
            let icon = document.getElementById("iconHistory");

            if (icon.classList.contains("bx-chevrons-right")) {
                icon.classList.remove("bx-chevrons-right");
                icon.classList.add("bx-chevrons-left");
                localStorage.setItem("historyDivisionState", "closed");
            } else {
                icon.classList.remove("bx-chevrons-left");
                icon.classList.add("bx-chevrons-right");
                localStorage.setItem("historyDivisionState", "open");
            }

            if (historyDiv.style.display === 'none') {
                historyDiv.style.display = 'block';
                divisionList.classList.remove('col-lg-12');
                divisionList.classList.add('col-lg-8');
                divisionContainer.classList.remove('row-cols-lg-1', 'mt-4');
                divisionContainer.classList.add('row-cols-lg-2', 'mt-0');
                historyIcon.classList.remove('d-flex');
                historyIcon.classList.add('d-none');
            } else {
                historyDiv.style.display = 'none';
                divisionList.classList.remove('col-lg-8');
                divisionList.classList.add('col-lg-12');
                divisionContainer.classList.remove('row-cols-lg-2', 'mt-0');
                divisionContainer.classList.add('row-cols-lg-1', 'mt-4');
                historyIcon.classList.remove('d-none');
                historyIcon.classList.add('d-flex');
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const state = localStorage.getItem("historyDivisionState");
            if (state === "closed") {
                historyDivision();
            }
        });
    </script>
@endpush
