@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0 mb-3">
        <a href="{{ route('memo.index') }}" class="breadcrumb-items">Arsip</a>
        <a href="{{ route('memo.show', $archive->archive_id) }}" class="breadcrumb-items">{{ $archive->archive_id }}</a>
        <a href="" class="breadcrumb-items active">{{ $navTitle }}</a>
    </ol>
    <!-- Breadcrumb End -->

    <div class="d-flex flex-column gap-2">
        <form action="{{ route('memo.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" class="form-control" id="archive_id" name="archive_id" value="{{ $archive->id }}">
            <input type="hidden" class="form-control" id="no_letter" name="no_letter" value="{{ $no_letter }}">
            <input type="hidden" class="form-control" id="letter_code" name="letter_code" value="{{ $letter_code }}">
            @if ($archive->category->name === 'Memo')
                <input type="hidden" class="form-control" id="type" name="type" value="memo">
            @else
                <input type="hidden" class="form-control" id="type" name="type" value="notulen">
            @endif

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

            @if ($archive->category->name === 'Memo')
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="card-title">Data</h4>
                        <hr class="bg-secondary">
                        <input type="hidden" name="notulis" value="{{ Auth::user()->name }}">
                        <div class="d-flex flex-column flex-md-row gap-3 w-100">
                            <div class="w-100">
                                <label for="name" class="form-label">Nama Memo<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-receipt'></i>
                                    </span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Masukkan nama memo" required>
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
                                        id="date" name="date" required>
                                    @error('date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="w-100">
                            <label for="content" class="form-label">Keterangan<strong class="text-danger">*</strong></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="5"></textarea>
                            @error('content')
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
                        <div class="d-flex flex-column flex-md-row gap-3 mb-3 w-100">
                            <div class="w-100">
                                <label for="name" class="form-label">Nama Notulen<strong
                                        class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-receipt'></i>
                                    </span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Masukkan nama notulen" required>
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
                                        id="event" name="event" placeholder="Masukkan nama acara" required>
                                    @error('event')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="place" class="form-label">Tempat<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-map'></i>
                                    </span>
                                    <input type="text" class="form-control @error('place') is-invalid @enderror" id="place" name="place" placeholder="Masukkan tempat" required>
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
                                <label for="chairman" class="form-label">Ketua Rapat<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-user'></i>
                                    </span>
                                    <input type="text" class="form-control @error('chairman') is-invalid @enderror" id="chairman" name="chairman" placeholder="Masukkan nama ketua" required>
                                    @error('chairman')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="chairman_position" class="form-label">Jabatan Ketua<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-briefcase'></i>
                                    </span>
                                    <input type="text" class="form-control @error('chairman_position') is-invalid @enderror" id="chairman_position" name="chairman_position" placeholder="Masukkan jabatan ketua" required>
                                    @error('chairman_position')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="notulis" class="form-label">Notulis<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-user'></i>
                                    </span>
                                    <input type="text" class="form-control @error('notulis') is-invalid @enderror" id="notulis" name="notulis" value="{{ Auth::user()->name }}" readonly required>
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
                                <label for="date" class="form-label">Tanggal<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-calendar-alt'></i>
                                    </span>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" required>
                                    @error('date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="start_time" class="form-label">Waktu Mulai<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-time'></i>
                                    </span>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" required>
                                    @error('start_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="w-100">
                                <label for="end_time" class="form-label">Waktu Selesai<strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1" style="width: 45px">
                                        <i class='bx bx-time'></i>
                                    </span>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" required>
                                    @error('end_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="w-100">
                            <label for="participant" class="form-label">Partisipan<strong class="text-danger">*</strong></label>
                            <textarea class="form-control @error('participant') is-invalid @enderror" id="participant" placeholder="Masukkan partisipan" rows="5" name="participant">{{ old('participant') }}</textarea>
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
                            <label for="content" class="form-label">Isi Rapat<strong class="text-danger">*</strong></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content">
                                {{ old('content') }}
                            </textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="w-100 mt-3">
                            <label for="decision" class="form-label">Hasil/Kesimpulan Rapat<strong class="text-danger">*</strong></label>
                            <textarea class="form-control @error('decision') is-invalid @enderror" id="decision" name="decision">{{ old('decision') }}</textarea>
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
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
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
