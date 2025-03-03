@extends('layouts.main')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        .select2-selection.select2-selection--single {
            height: 40px !important;
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
                    <h4 class="card-title">Gambar</h4>
                    <hr class="bg-secondary">
                    <div class="previwe-img d-flex justify-content-center mb-2 py-3">
                        @if ($letter->image)
                            <img src="{{ url('storage/letter_image/' . $letter->image) }}" alt="image preview"
                                id="img" style="width: 120px;">
                        @else
                            <img src="{{ url('assets/img/logo_ppj.png') }}" alt="image preview" id="img"
                                style="width: 120px;">
                        @endif
                    </div>
                    <div class="mb-2">
                        <input type="file" accept="image/*" class="form-control @error('image') is-invalid @enderror"
                            id="input-img" name="image">
                        @error('image')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title">File</h4>
                    <hr class="bg-secondary">
                    <div class="mb-2">
                        <input type="file" accept=".doc,.docx,.pdf"
                            class="form-control @error('file') is-invalid @enderror" name="file">
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
                    <div class="mb-3">
                        <label for="status" class="form-label">Status<strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1" style="width: 45px;">
                                <i class='bx bx-loader-circle'></i>
                            </span>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
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
                    <div class="w-100 mb-3">
                        <label for="item_id" class="form-label">Inventory</label>
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
                    <div class="d-flex flex-column flex-md-row gap-3 w-100">
                        <div class="w-100">
                            <label for="name" class="form-label">Nama Surat<strong
                                    class="text-danger">*</strong></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class="bx bx-box"></i>
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
        $('#inventorySelect').change(function() {
            var selectedValues = $(this).val();
            $('#item_id').val(selectedValues);
        });
    </script>
@endpush
