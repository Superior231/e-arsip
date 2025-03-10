@extends('layouts.main')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css">

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
        @if (!empty($letter_id))
            <a href="{{ route('letter.show', $letter->no_letter) }}" class="breadcrumb-items">{{ $letter->no_letter }}</a>
        @endif
        <a href="" class="breadcrumb-items active">{{ $navTitle }}</a>
    </ol>
    <!-- Breadcrumb End -->

    <div class="d-flex flex-column gap-2">
        <form action="{{ route('letter.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" class="form-control" id="archive_id" name="archive_id" value="{{ $archive->id }}">
            <input type="hidden" class="form-control" id="no_letter" name="no_letter" value="{{ $no_letter }}">
            <input type="hidden" class="form-control" id="letterCodeOut" name="letter_code" value="{{ $letter_code }}">
            <input type="hidden" class="form-control" name="letter_id" value="{{ $letter_id ?? '' }}">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Dokumen</h4>
                    <hr class="bg-secondary">
                    <div class="mb-2">
                        <input type="file" multiple accept=".jpg,.jpeg,.png,.webp,.doc,.docx,.pdf"
                            class="form-control @error('file') is-invalid @enderror" name="file[]">
                        @error('file')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title">Data</h4>
                    <hr class="bg-secondary">
                    @if ($archive->category->name === 'Faktur')
                        <div class="w-100 mb-3">
                            <label for="inventorySelect" class="form-label">Inventory</label>
                            <div class="d-flex" style="width: 100%;">
                                <span class="input-group-text" for="inventorySelect"
                                    style="width: 45px; border-radius: 5px 0px 0px 5px;">
                                    <i class='bx bx-box'></i>
                                </span>
                                <select class="form-select @error('item_id') is-invalid @enderror" name="item_id"
                                    id="inventorySelect">
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->inventory->name }} =>
                                            {{ $item->name }}
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

                    <div class="w-100 mb-3 d-none" id="letterCodeContainer">
                        <label for="name" class="form-label">Kode Surat<strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                <i class='bx bx-code-alt'></i>
                            </span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="letterCodeIn"
                                name="letter_code" value="" placeholder="Masukkan kode surat">
                            @error('letter_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="w-100">
                        <div class="d-flex flex-column flex-md-row gap-3 w-100">
                            <div class="w-100">
                                <label for="name" class="form-label">Nama Surat<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-receipt'></i>
                                    </span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Masukkan nama surat" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="type" class="form-label">Type Surat<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-receipt'></i>
                                    </span>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type"
                                        type="type" name="type" required>
                                        <option value="">Pilih</option>
                                        <option value="letter_in">Surat Masuk</option>
                                        <option value="letter_out">Surat Keluar</option>
                                        <option value="faktur">Faktur</option>
                                    </select>
                                    @error('type')
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
                                        id="date" name="date" required>
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
                            <textarea class="form-control @error('detail') is-invalid @enderror" id="detail" name="detail" rows="3"></textarea>
                            @error('detail')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 d-none" id="letterContent">
                <div class="card-body">
                    <h4 class="card-title">Isi Surat</h4>
                    <hr class="bg-secondary">
                    <textarea name="content" id="content">{{ old('content') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('archive.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $('#inventorySelect').select2({
            tags: false,
            placeholder: "Select item",
            allowClear: true
        });

        document.addEventListener("DOMContentLoaded", function() {
            const typeSelect = document.getElementById("type");
            const letterCodeContainer = document.getElementById("letterCodeContainer");
            const letterCodeIn = document.getElementById("letterCodeIn");
            const letterCodeOut = document.getElementById("letterCodeOut");
            const letterContent = document.getElementById("letterContent");

            typeSelect.addEventListener("change", function() {
                if (this.value === "letter_out") {
                    letterCodeContainer.classList.remove("d-block");
                    letterCodeContainer.classList.add("d-none");
                    letterCodeIn.setAttribute('name', '');
                    letterCodeOut.setAttribute('name', 'letter_code');
                    letterContent.classList.remove("d-none");
                } else if (this.value === "letter_in") {
                    letterCodeContainer.classList.remove("d-none");
                    letterCodeContainer.classList.add("d-block");
                    letterCodeIn.setAttribute('name', 'letter_code');
                    letterCodeOut.setAttribute('name', '');
                    letterContent.classList.add("d-none");
                } else {
                    letterCodeContainer.classList.remove("d-block");
                    letterCodeContainer.classList.add("d-none");
                    letterCodeOut.setAttribute('name', 'letter_code');
                    letterCodeIn.setAttribute('name', '');
                    letterContent.classList.add("d-none");
                }
            });
        });
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
