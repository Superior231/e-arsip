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
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0">
        <a href="{{ route('category.index') }}" class="breadcrumb-items">Kategori</a>
        <a href="" class="breadcrumb-items active">{{ $category->name }}</a>
    </ol>
    <!-- Breadcrumb End -->

    <div class="history-icon mb-2 d-none" onclick="historySubCategory()">
        <div class="d-flex align-items-center">
            <i class='bx bx-chevrons-right fs-3' id="iconHistory"></i>
            <span class="my-0 py-0">History</span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-lg-2 g-3 mt-0" id="subCategoryContainer">
        <div class="col col-12 col-lg-8" id="subCategoryList">
            <div class="card p-4 pt-3">
                <div class="actions d-flex align-items-center justify-content-between">
                    <h4 class="fw-semibold py-0 my-0">Daftar Sub Kategori</h4>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal"
                        data-bs-target="#createSubCategoryModal" onclick="createSubCategory('{{ $category->id }}')">
                        <i class='bx bx-plus'></i>
                        Sub kategori
                    </button>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="myDataTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-nowrap">Nama</th>
                                <th>Kode</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($category->subCategories as $subCategory)
                                <tr class="align-middle">
                                    <td>
                                        <span>{{ $subCategory->name }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $category->slug }}.{{ $subCategory->slug }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center pe-2">
                                            <span
                                                class="badge {{ $subCategory->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $subCategory->status }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-none">{{ $subCategory->created_at }}</span>
                                        <div class="d-flex justify-content-center align-items-center pe-3">
                                            <span>{{ $subCategory->created_at->format('d M Y H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions d-flex justify-content-center pe-3 gap-2">
                                            <button type="button" class="btn btn-primary d-flex align-items-center p-2"
                                                data-bs-toggle="modal" data-bs-target="#editSubCategoryModal"
                                                onclick="editSubCategory('{{ $subCategory->id }}', '{{ $subCategory->status }}', '{{ $subCategory->name }}')">
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

        <div class="col col-12 col-lg-4" id="historySubCategory">
            <div class="card">
                <div class="card-body px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-semibold my-0 py-0 px-3">Riwayat Sub Kategori</h4>
                        <i class='bx bx-chevrons-right fs-3 px-3' id="iconHistory" onclick="historySubCategory()"
                            style="cursor: pointer;"></i>
                    </div>
                    <hr class="pb-0 mb-0">
                    <div class="d-flex flex-column" style="max-height: 500px; overflow-y: scroll">
                        @forelse ($history_subcategory as $history)
                            @php
                                $bgClass = match ($history->method) {
                                    'create' => 'bg-success text-light',
                                    'update' => 'bg-warning',
                                    'mutate' => 'bg-primary text-light',
                                    'mutate, update' => 'bg-dark text-light',
                                    'mutate, delete' => 'bg-danger text-light',
                                    'delete' => 'bg-danger text-light',
                                    default => 'bg-secondary text-dark',
                                };
                            @endphp

                            <div class="notification-header d-flex justify-content-between {{ $bgClass }} px-3">
                                <span class="fs-7">{{ $history->title }}</span>
                                <span class="fs-7">{{ $history->created_at->format('d M Y H:i') }} WIB</span>
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
    <div class="modal fade" id="createSubCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('subcategory.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCategoryModalLabel">Buat Sub Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="add_category_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama kategori</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-purchase-tag'></i>
                                </span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" aria-describedby="name" value="{{ old('name') }}"
                                    placeholder="Masukkan nama kategori">
                                @error('name')
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

    <div class="modal fade" id="editSubCategoryModal" tabindex="-1" aria-labelledby="editSubCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSubCategoryForm" method="POST">
                    @csrf @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editSubCategoryModalLabel">Edit Sub Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="edit_category_id">
                        <div class="mb-3">
                            <label for="editSubCategoryStatus" class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-loader bx-rotate'></i>
                                </span>
                                <select class="form-select @error('status') is-invalid @enderror" id="editSubCategoryStatus"
                                    name="status">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editSubCategoryName" class="form-label">Nama sub kategori</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-label'></i>
                                </span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="editSubCategoryName" name="name" aria-describedby="name"
                                    value="{{ old('name') }}" placeholder="Masukkan nama kategori">
                                @error('name')
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

        function createSubCategory(categoryId) {
            $('#add_category_id').val(categoryId);
        }

        function editSubCategory(id, status, name) {
            $('#editSubCategoryStatus').val(status);
            $('#editSubCategoryName').val(name);

            $('#editSubCategoryForm').attr('action', "{{ route('subcategory.update', '') }}" + '/' + id);
            $('#editSubCategoryModal').modal('show');
        }

        function historySubCategory() {
            let historyDiv = document.getElementById('historySubCategory');
            let subCategoryList = document.getElementById('subCategoryList');
            let subCategoryContainer = document.getElementById('subCategoryContainer');
            let historyIcon = document.querySelector('.history-icon');
            let icon = document.getElementById("iconHistory");

            if (icon.classList.contains("bx-chevrons-right")) {
                icon.classList.remove("bx-chevrons-right");
                icon.classList.add("bx-chevrons-left");
                localStorage.setItem("historySubCategoryState", "closed");
            } else {
                icon.classList.remove("bx-chevrons-left");
                icon.classList.add("bx-chevrons-right");
                localStorage.setItem("historySubCategoryState", "open");
            }

            if (historyDiv.style.display === 'none') {
                historyDiv.style.display = 'block';
                subCategoryList.classList.remove('col-lg-12');
                subCategoryList.classList.add('col-lg-8');
                subCategoryContainer.classList.remove('row-cols-lg-1');
                subCategoryContainer.classList.add('row-cols-lg-2');
                historyIcon.classList.remove('d-flex');
                historyIcon.classList.add('d-none');
            } else {
                historyDiv.style.display = 'none';
                subCategoryList.classList.remove('col-lg-8');
                subCategoryList.classList.add('col-lg-12');
                subCategoryContainer.classList.remove('row-cols-lg-2');
                subCategoryContainer.classList.add('row-cols-lg-1');
                historyIcon.classList.remove('d-none');
                historyIcon.classList.add('d-flex');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const state = localStorage.getItem("historySubCategoryState");
            if (state === "closed") {
                historySubCategory();
            }
        });
    </script>
@endpush
