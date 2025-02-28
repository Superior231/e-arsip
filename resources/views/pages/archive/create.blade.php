@extends('layouts.main')

@section('content')
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0 mb-3">
        <a href="{{ route('archive.index') }}" class="breadcrumb-items">Arsip</a>
        <a href="" class="breadcrumb-items active">{{ $navTitle }}</a>
    </ol>
    <!-- Breadcrumb End -->


    <div class="d-flex flex-column gap-2">
        <form action="{{ route('archive.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" name="archive_id" value="">
            <input type="hidden" name="archive_code" value="">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Gambar</h4>
                    <hr class="bg-secondary">
                    <div class="previwe-img d-flex justify-content-center mb-2 py-3">
                        <img src="{{ url('assets/img/logo_ppj.png') }}" alt="image preview" id="img"
                            style="width: 120px;">
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
                    <h4 class="card-title">Data</h4>
                    <hr class="bg-secondary">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Arsip<strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1" style="width: 45px;">
                                <i class='bx bx-archive'></i>
                            </span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Masukan nama aset" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row align-items-center gap-3 w-100">
                        <div class="w-100">
                            <label for="division_id" class="form-label">Nama Divisi<strong
                                    class="text-danger">*</strong></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px;">
                                    <i class='bx bx-buildings'></i>
                                </span>
                                <select class="form-select @error('division_id') is-invalid @enderror" id="division_id"
                                    name="division_id" required>
                                    <option value="">Pilih</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ $division->status == 'active' ? '' : 'disabled' }}>
                                            {{ $division->name }} ({{ $division->place }})
                                            {!! $division->status === 'active' ? '' : ' ~ ⚠ Inactive' !!}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="w-100">
                            <label for="category_id" class="form-label">Kategori<strong
                                    class="text-danger">*</strong></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px;">
                                    <i class='bx bx-purchase-tag'></i>
                                </span>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Pilih</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->status == 'active' ? '' : 'disabled' }}>
                                            {{ $category->name }}
                                            {!! $category->status === 'active' ? '' : ' ~ ⚠ Inactive' !!}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="w-100">
                            <label for="date" class="form-label">Tanggal<strong class="text-danger">*</strong></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                    <i class='bx bx-time-five'></i>
                                </span>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" required>
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('archive.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
@endsection
