@extends('layouts.main')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .select2-selection.select2-selection--single.select2-selection--clearable {
            border-radius: 0px 5px 5px 0px !important;
        }

        @media (min-width: 520px) {
            .select2.select2-container.select2-container--default {
                width: 100% !important;
            }
        }

        .select2-selection.select2-selection--single {
            height: 40px !important;
            padding: 0px 5px !important;
        }

        .select2-selection__rendered,
        .select2-selection__clear,
        .select2-selection__arrow {
            height: 100% !important;
            line-height: 40px !important;
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0 mb-3">
        <a href="{{ route('archive.index') }}" class="breadcrumb-items">Arsip</a>
        <a href="{{ route('archive.show', $archive->archive_id) }}" class="breadcrumb-items">{{ $archive->archive_id }}</a>
        <a href="{{ route('letter.show', $letter->no_letter) }}" class="breadcrumb-items">{{ $letter->no_letter }}</a>
        <a href="" class="breadcrumb-items active">{{ $navTitle }}</a>
    </ol>
    <!-- Breadcrumb End -->

    <div class="d-flex flex-column gap-2">
        <form action="{{ route('letter.update', $letter->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" class="form-control" id="archive_id" name="archive_id" value="{{ $archive->id }}">
            <input type="hidden" class="form-control" id="no_letter" name="no_letter" value="{{ $letter->no_letter }}">
            <input type="hidden" class="form-control" id="letter_code" name="letter_code"
                value="{{ $letter->letter_code }}">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title py-0 my-0">Dokumen</h4>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                            <i class='bx bx-plus fs-5'></i>
                            <span class="my-0 py-0">Tambah</span>
                        </button>
                    </div>
                    <hr class="bg-secondary">
                    <div class="mb-2">
                        <div class="table-responsive">
                            <table id="myDataTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">File</th>
                                        <th class="text-nowrap">Name</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents->where('status', 'active') as $document)
                                        <tr class="align-middle">
                                            <td>
                                                <div class="position-relative d-flex justify-content-center">
                                                    @if ($document->type === 'image')
                                                        <div class="image d-flex justify-content-center"
                                                            style="cursor: pointer;" data-bs-toggle="modal"
                                                            data-bs-target="#showImageModal"
                                                            onclick="showImages([{{ json_encode(asset('storage/documents/' . $document->file)) }}])">
                                                            <img src="{{ asset('storage/documents/' . $document->file) }}"
                                                                alt="gambar" class="img-fluid">
                                                        </div>
                                                    @elseif (Str::endsWith($document->file, ['.doc', '.docx']))
                                                        <div class="image d-flex justify-content-center align-items-center"
                                                            style="cursor: pointer;">
                                                            <i class='bx bxs-file-doc text-primary fs-2 text-center'></i>
                                                        </div>
                                                    @else
                                                        <div class="image d-flex justify-content-center align-items-center"
                                                            style="cursor: pointer;">
                                                            <i class='bx bxs-file-pdf text-danger fs-2 text-center'></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-break">{{ $document->file }}</span>
                                            </td>
                                            <td>
                                                <div class="actions d-flex justify-content-center">
                                                    <div class="dropdown pe-3">
                                                        <i class="bx bx-cog fs-4" id="action-{{ $document->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false"
                                                            style="cursor: pointer;" title="Actions"></i>

                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="action-{{ $document->id }}">
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1"
                                                                    href="#">
                                                                    <i class='bx bx-arrow-to-bottom fs-5'></i>
                                                                    Download
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editDocumentModal"
                                                                    style="cursor: pointer;"
                                                                    onclick="editDocument(
                                                                    '{{ $document->id }}',
                                                                    '{{ $document->file }}')">
                                                                    <i class='bx bx-pencil fs-5'></i>
                                                                    Edit data
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a style="cursor: pointer;"
                                                                    class="dropdown-item d-flex align-items-center gap-1"
                                                                    onclick="confirmDeleteDocument({{ $document->id }})">
                                                                    <i class='bx bx-trash fs-5'></i> Hapus
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
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title">Data</h4>
                    <hr class="bg-secondary">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status<strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1" style="width: 45px;">
                                <i class='bx bx-loader-circle'></i>
                            </span>
                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                name="status">
                                <option value="active" {{ $letter->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $letter->status == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                                <option value="rusak" {{ $letter->status == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                <option value="hilang" {{ $letter->status == 'hilang' ? 'selected' : '' }}>Hilang</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    @if ($archive->category->name === 'Faktur')
                        <div class="w-100 mb-3">
                            <label for="inventorySelect" class="form-label">Inventory</label>
                            <div class="d-flex" style="width: 100%;">
                                <span class="input-group-text" for="inventorySelect"
                                    style="width: 45px; border-radius: 5px 0px 0px 5px;">
                                    <i class='bx bx-box'></i>
                                </span>
                                <select class="form-select @error('item_id') is-invalid @enderror" id="inventorySelect"
                                    name="item_id">
                                    <option value="">Pilih</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $letter->item_id === $item->id ? 'selected' : '' }}>
                                            {{ $item->inventory->name }} => {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <div class="d-flex flex-column flex-md-row gap-3 w-100">
                        <div class="w-100">
                            <label for="name" class="form-label">Nama Surat<strong
                                    class="text-danger">*</strong></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-receipt'></i>
                                </span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Masukkan nama surat" required
                                    value="{{ $letter->name }}">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-100 mb-3">
                            <label for="date" class="form-label">Tanggal<strong
                                    class="text-danger">*</strong></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-time-five'></i>
                                </span>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" required value="{{ $letter->date }}">
                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="detail" class="form-label">Detail</label>
                        <textarea class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail" rows="3">{{ $letter->detail }}</textarea>
                        @error('detail')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('archive.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>

    <form id="deleteDocumentForm" action="" method="POST"
        style="display: none;">
        @csrf 
        @method('PUT')
            <input type="hidden" class="form-control" id="status" name="status" value="delete">
    </form>

    <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addDocumentModalLabel">Tambah Dokumen</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="letter_id" value="{{ $letter->id }}">
                        <input type="file" multiple accept=".jpg,.jpeg,.png,.webp,.doc,.docx,.pdf"
                            class="form-control @error('file') is-invalid @enderror" name="file[]">
                        @error('file')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editDocumentModalLabel">Update Dokumen</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data" id="editDocumentForm">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="letter_id" value="{{ $letter->id }}">
                        <span id="editFileName"></span>
                        <input type="file" accept=".jpg,.jpeg,.png,.webp,.doc,.docx,.pdf"
                            class="form-control @error('file') is-invalid @enderror" name="file" id="editFile">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

        $('#inventorySelect').select2({
            tags: false,
            placeholder: "Select item",
            allowClear: true
        });

        function editDocument(id, file) {
            $('#editFile').val('');
            $('#editFileName').text(file);
            $('#editDocumentForm').attr('action', "{{ route('document.update', '') }}" + '/' + id);
        }

        function confirmDeleteDocument(id) {
            Swal.fire({
                icon: "question",
                title: "Apakah Anda yakin?",
                text: "Apakah Anda yakin ingin menghapus dokumen ini?",
                showCancelButton: true,
                confirmButtonText: 'Delete',
                customClass: {
                    popup: 'sw-popup',
                    title: 'sw-title',
                    closeButton: 'sw-close',
                    confirmButton: 'btn-danger bg-danger',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('deleteDocumentForm');
                    form.action = "{{ route('document.delete', '') }}" + '/' + id;
                    form.submit();
                }
            });
        }
    </script>
@endpush
