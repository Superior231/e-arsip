@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0 mb-3">
        <a href="{{ route('memo.index') }}" class="breadcrumb-items">Arsip</a>
        <a href="{{ route('memo.show', $archive->archive_id) }}" class="breadcrumb-items">{{ $archive->archive_id }}</a>
        <a href="{{ route('memo.show', $letter->no_letter) }}" class="breadcrumb-items">{{ $letter->no_letter }}</a>
        <a href="" class="breadcrumb-items active">{{ $navTitle }}</a>
    </ol>
    <!-- Breadcrumb End -->

    <div class="d-flex flex-column gap-2">
        <form action="{{ route('memo.update', $letter->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" class="form-control" id="archive_id" name="archive_id" value="{{ $archive->id }}">
            <input type="hidden" class="form-control" id="no_letter" name="no_letter" value="{{ $letter->no_letter }}">
            <input type="hidden" class="form-control" id="letter_code" name="letter_code"
                value="{{ $letter->letter_code }}">
            <input type="hidden" class="form-control" id="type" name="type" value="{{ $letter->type }}">

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
                                        <th class="text-center">File</th>
                                        <th class="text-nowrap">Name</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents->where('status', '!=', 'delete') as $document)
                                        <tr class="align-middle">
                                            <td>
                                                <div class="position-relative d-flex justify-content-center">
                                                    <div class="image d-flex justify-content-center align-items-center"
                                                        style="cursor: pointer;">
                                                        @if ($document->type === 'image')
                                                            <img src="{{ asset('storage/documents/' . $document->file) }}"
                                                                alt="gambar" class="img-fluid">
                                                        @elseif (Str::endsWith($document->file, ['.doc', '.docx']))
                                                            <i class='bx bxs-file-doc text-primary fs-2 text-center'></i>
                                                        @else
                                                            <i class='bx bxs-file-pdf text-danger fs-2 text-center'></i>
                                                        @endif
                                                    </div>
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
                                                                    href="{{ asset('storage/documents/' . $document->file) }}"
                                                                    download="{{ $document->file }}">
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

            @if (Auth::user()->roles === 'superadmin')
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="w-100">
                            <label for="status" class="form-label">Status<strong class="text-danger">*</strong></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px;">
                                    <i class='bx bx-loader-circle'></i>
                                </span>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    <option value="approve" {{ $letter->status == 'approve' ? 'selected' : '' }}>Approve
                                    </option>
                                    <option value="pending" {{ $letter->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($archive->category->name === 'Memo')
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card-title">Data</h4>
                        <hr class="bg-secondary">
                        <input type="hidden" name="notulis" value="{{ $letter->notulis }}">
                        <div class="d-flex flex-column flex-md-row gap-3 w-100">
                            <div class="w-100">
                                <label for="name" class="form-label">Nama Memo<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-receipt'></i>
                                    </span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ $letter->name }}" required>
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
                                        id="date" name="date" value="{{ $letter->date }}" required>
                                    @error('date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="w-100">
                            <label for="detail" class="form-label">Keterangan<strong
                                    class="text-danger">*</strong></label>
                            <textarea class="form-control @error('detail') is-invalid @enderror" name="detail" rows="3">{{ $letter->detail }}</textarea>
                            @error('detail')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            @else
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card-title">Data</h4>
                        <hr class="bg-secondary">
                        <input type="hidden" name="notulis" value="{{ $letter->notulis }}">
                        <div class="d-flex flex-column flex-md-row gap-3 mb-3 w-100">
                            <div class="w-100">
                                <label for="name" class="form-label">Nama Notulen<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-receipt'></i>
                                    </span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ $letter->name }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="event" class="form-label">Acara<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-receipt'></i>
                                    </span>
                                    <input type="text" class="form-control @error('event') is-invalid @enderror"
                                        id="event" name="event" value="{{ $letter->event }}" required>
                                    @error('event')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="place" class="form-label">Tempat<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-map'></i>
                                    </span>
                                    <input type="text" class="form-control @error('place') is-invalid @enderror"
                                        id="place" name="place" value="{{ $letter->place }}" required>
                                    @error('place')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-3 mb-3 w-100">
                            <div class="w-100">
                                <label for="chairman" class="form-label">Ketua Rapat<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-user'></i>
                                    </span>
                                    <input type="text" class="form-control @error('chairman') is-invalid @enderror"
                                        id="chairman" name="chairman" value="{{ $letter->chairman }}" required>
                                    @error('chairman')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="chairman_position" class="form-label">Jabatan Ketua<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-briefcase'></i>
                                    </span>
                                    <input type="text"
                                        class="form-control @error('chairman_position') is-invalid @enderror"
                                        id="chairman_position" name="chairman_position"
                                        value="{{ $letter->chairman_position }}" required>
                                    @error('chairman_position')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="notulis" class="form-label">Notulis<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-user'></i>
                                    </span>
                                    <input type="text" class="form-control @error('notulis') is-invalid @enderror"
                                        id="notulis" name="notulis" value="{{ Auth::user()->name }}" readonly
                                        required>
                                    @error('notulis')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-3 mb-3 w-100">
                            <div class="w-100">
                                <label for="date" class="form-label">Tanggal<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-calendar-alt'></i>
                                    </span>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        id="date" name="date" value="{{ $letter->date }}" required>
                                    @error('date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="start_time" class="form-label">Waktu Mulai<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-time'></i>
                                    </span>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time" value="{{ $letter->start_time }}" required>
                                    @error('start_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="end_time" class="form-label">Waktu Selesai<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-time'></i>
                                    </span>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time" value="{{ $letter->end_time }}" required>
                                    @error('end_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="w-100">
                            <label for="participant" class="form-label">Partisipan<strong
                                    class="text-danger">*</strong></label>
                            <textarea class="form-control @error('participant') is-invalid @enderror" id="participant" name="participant">{{ $letter->participant }}</textarea>
                            @error('participant')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card-title">Content</h4>
                        <hr class="bg-secondary">
                        <div class="w-100">
                            <label for="content" class="form-label">Isi Rapat<strong
                                    class="text-danger">*</strong></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content">{{ $letter->content }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="w-100 mt-3">
                            <label for="decision" class="form-label">Kesimpulan Rapat<strong
                                    class="text-danger">*</strong></label>
                            <textarea class="form-control @error('decision') is-invalid @enderror" id="decision" name="decision">{{ $letter->decision }}</textarea>
                            @error('decision')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('memo.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>



    <form id="deleteDocumentForm" action="" method="POST" style="display: none;">
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

    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.js",
                "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.2/"
            }
        }
    </script>
    <script type="module" src="{{ url('assets/js/ckeditor.js') }}"></script>
@endpush
