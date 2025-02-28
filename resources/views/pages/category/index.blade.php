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
    <div class="history-icon mb-2 d-none" onclick="historyCategory()">
        <div class="d-flex align-items-center">
            <i class='bx bx-chevrons-right fs-3' id="iconHistory"></i>
            <span class="my-0 py-0">History</span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-lg-2 g-3 mt-0" id="categoryContainer">
        <div class="col col-12 col-lg-8" id="categoryList">
            <div class="card p-4 pt-3">
                <div class="actions d-flex align-items-center justify-content-between">
                    <h4 class="fw-semibold py-0 my-0">Daftar Kategori</h4>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal"
                        data-bs-target="#createCategoryModal">
                        <i class='bx bx-plus'></i>
                        Kategori
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
                            @foreach ($categories as $category)
                                <tr class="align-middle">
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $category->name }}</span>
                                            @if ($category->subCategories->isNotEmpty())
                                                <a class="text-decoration-underline" style="cursor: pointer;"
                                                    id="showItem-{{ $category->id }}"
                                                    onclick="showItems({{ $category->id }})">
                                                    <span>Lihat items</span>
                                                </a>

                                                <div class="show-items-container d-none flex-column"
                                                    id="showItemContainer-{{ $category->id }}">
                                                    @foreach ($category->subCategories as $item)
                                                        <span class="py-0 my-0">- {{ $item->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span>{{ $category->slug }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center pe-2">
                                            <span
                                                class="badge {{ $category->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $category->status }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-none">{{ $category->created_at }}</span>
                                        <div class="d-flex justify-content-center align-items-center pe-3">
                                            <span>{{ $category->created_at->format('d M Y H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions d-flex justify-content-center pe-3">
                                            <div class="dropdown">
                                                <i class="bx bx-cog fs-4" id="action-{{ $category->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"
                                                    title="Actions"></i>

                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="action-{{ $category->id }}">
                                                    <li>
                                                        <div class="d-flex justify-content-center mb-1 fw-bold">
                                                            {{ $category->name }}
                                                        </div>
                                                    </li>
                                                    <hr class="dropdown-divider py-0 my-0">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                                            href="{{ route('category.show', $category->slug) }}">
                                                            <i class='bx bx-show fs-5'></i>
                                                            Lihat detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a style="cursor: pointer;"
                                                            class="dropdown-item d-flex align-items-center gap-1"
                                                            data-bs-toggle="modal" data-bs-target="#createSubCategoryModal"
                                                            onclick="createSubCategory('{{ $category->id }}')">
                                                            <i class='bx bx-plus fs-5'></i>
                                                            Tambah sub kategori
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a style="cursor: pointer;"
                                                            class="dropdown-item d-flex align-items-center gap-1"
                                                            data-bs-toggle="modal" data-bs-target="#editCategoryonModal"
                                                            onclick="editCategory('{{ $category->id }}', '{{ $category->status }}', '{{ $category->name }}')">
                                                            <i class='bx bx-pencil fs-5'></i>
                                                            Edit data
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col col-12 col-lg-4" id="historyCategory">
            <div class="card">
                <div class="card-body px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-semibold my-0 py-0 px-3">Riwayat Kategori</h4>
                        <i class='bx bx-chevrons-right fs-3 px-3' id="iconHistory" onclick="historyCategory()"
                            style="cursor: pointer;"></i>
                    </div>
                    <hr class="pb-0 mb-0">
                    <div class="d-flex flex-column" style="max-height: 500px; overflow-y: scroll">
                        @forelse ($histories_categories as $history)
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
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('category.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="createCategoryModalLabel">Buat Kategori</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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

    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editCategoryForm" method="POST">
                    @csrf @method('PUT')

                    <div class="modal-header">
                        <h4 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editCategoryStatus" class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-loader bx-rotate'></i>
                                </span>
                                <select class="form-select @error('status') is-invalid @enderror" id="editCategoryStatus"
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
                            <label for="editCategoryName" class="form-label">Nama kategori</label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-label'></i>
                                </span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="editCategoryName" name="name" aria-describedby="name"
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

    <div class="modal fade" id="createSubCategoryModal" tabindex="-1" aria-labelledby="createSubCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('subcategory.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="createSubCategoryModalLabel">Buat Sub Kategori</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="category_id" id="add_category_id">
                            <label for="name" class="form-label">Nama sub kategori</label>
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

        function showItems(categoryId) {
            var container = document.getElementById('showItemContainer-' + categoryId);

            container.classList.toggle('d-none');
            container.classList.toggle('d-flex');
        }

        function editCategory(id, status, name) {
            $('#editCategoryStatus').val(status);
            $('#editCategoryName').val(name);

            $('#editCategoryForm').attr('action', "{{ route('category.update', '') }}" + '/' + id);
            $('#editCategoryModal').modal('show');
        }

        function createSubCategory(categoryId) {
            $('#add_category_id').val(categoryId);
        }

        function historyCategory() {
            let historyDiv = document.getElementById('historyCategory');
            let categoryList = document.getElementById('categoryList');
            let categoryContainer = document.getElementById('categoryContainer');
            let historyIcon = document.querySelector('.history-icon');
            let icon = document.getElementById("iconHistory");

            if (icon.classList.contains("bx-chevrons-right")) {
                icon.classList.remove("bx-chevrons-right");
                icon.classList.add("bx-chevrons-left");
                localStorage.setItem("historyCategoryState", "closed");
            } else {
                icon.classList.remove("bx-chevrons-left");
                icon.classList.add("bx-chevrons-right");
                localStorage.setItem("historyCategoryState", "open");
            }

            if (historyDiv.style.display === 'none') {
                historyDiv.style.display = 'block';
                categoryList.classList.remove('col-lg-12');
                categoryList.classList.add('col-lg-8');
                categoryContainer.classList.remove('row-cols-lg-1', 'mt-4');
                categoryContainer.classList.add('row-cols-lg-2', 'mt-0');
                historyIcon.classList.remove('d-flex');
                historyIcon.classList.add('d-none');
            } else {
                historyDiv.style.display = 'none';
                categoryList.classList.remove('col-lg-8');
                categoryList.classList.add('col-lg-12');
                categoryContainer.classList.remove('row-cols-lg-2', 'mt-0');
                categoryContainer.classList.add('row-cols-lg-1', 'mt-4');
                historyIcon.classList.remove('d-none');
                historyIcon.classList.add('d-flex');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const state = localStorage.getItem("historyCategoryState");
            if (state === "closed") {
                historyCategory();
            }
        });
    </script>
@endpush
