@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        table.table-info tr td>* {
            margin: 0px !important;
            padding: 0px !important;
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0 mb-3">
        <a href="{{ route('archive.index') }}" class="breadcrumb-items">Arsip</a>
        <a href="{{ route('archive.show', $letter->archive->archive_id) }}"
            class="breadcrumb-items">{{ $letter->archive->archive_id }}</a>
        <a href="" class="breadcrumb-items">{{ $letter->no_letter }}</a>
    </ol>
    <!-- Breadcrumb End -->

    @if ($letter_reply->isEmpty() && $letter->type == 'letter_in')
        <div class="alert alert-warning d-flex align-items-center alert-dismissible fade show" role="alert">
            <i class="bx bx-info-circle fs-3 me-2"></i>
            <div>
                Surat ini belum ada balasan. <a href="{{ route('letter.reply', $letter->no_letter) }}"
                    class="alert-link">Balas.</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body mb-3">
            <header class="mb-0 mb-md-4">
                <div class="logo d-flex justify-content-center">
                    <img src="{{ url('assets/img/logo_ppj.png') }}" alt="image preview" id="image-preview"
                        style="width: 120px;">
                </div>
                <div class="d-flex flex-column align-items-center">
                    <h4 class="fw-bold">CV. Putra Panggil Jaya</h4>
                    <h4 class="fw-bold text-center">Jl. Kolonel Sugiono No. 15 Slawi - Tegal</h4>
                    <h4 class="fw-bold">(0283) 492131-4561266</h4>
                </div>
            </header>

            <div class="d-flex flex-column-reverse flex-md-row justify-content-between align-items-center gap-2">
                <table class="d-flex align-items-center table-info">
                    <tr>
                        <td>
                            <h5>Status</h5>
                        </td>
                        <td>
                            <h5>&nbsp;:&nbsp;</h5>
                        </td>
                        <td>
                            <h5>
                                <span
                                    class="badge fs-8 
                            {{ $letter->status == 'approve' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $letter->status }}
                                </span>
                            </h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5>No Surat</h5>
                        </td>
                        <td>
                            <h5>&nbsp;:&nbsp;</h5>
                        </td>
                        <td>
                            <h5>{{ $letter->no_letter }}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5>Kode Surat</h5>
                        </td>
                        <td>
                            <h5>&nbsp;:&nbsp;</h5>
                        </td>
                        <td>
                            <h5>{{ $letter->letter_code }}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5>Type Surat</h5>
                        </td>
                        <td>
                            <h5>&nbsp;:&nbsp;</h5>
                        </td>
                        <td>
                            <h5>
                                @if ($letter->type == 'letter_in')
                                    Surat Masuk
                                @elseif ($letter->type == 'letter_out')
                                    Surat Keluar
                                @elseif ($letter->type == 'faktur')
                                    Faktur
                                @elseif ($letter->type == 'memo')
                                    Memo
                                @elseif ($letter->type == 'notulen')
                                    Notulen
                                @endif
                            </h5>
                        </td>
                    </tr>
                    @if ($letter->archive->category->name === 'Faktur')
                        <tr>
                            <td>
                                <h5>Inventory</h5>
                            </td>
                            <td>
                                <h5>&nbsp;:&nbsp;</h5>
                            </td>
                            <td>
                                <h5>{{ $letter->item->inventory->name }}</h5>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5>Item</h5>
                            </td>
                            <td>
                                <h5>&nbsp;:&nbsp;</h5>
                            </td>
                            <td>
                                <h5>{{ $letter->item->name }}</h5>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>
                            <h5>Nama Surat</h5>
                        </td>
                        <td>
                            <h5>&nbsp;:&nbsp;</h5>
                        </td>
                        <td>
                            <h5>{{ $letter->name }}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5>Tanggal</h5>
                        </td>
                        <td>
                            <h5>&nbsp;:&nbsp;</h5>
                        </td>
                        <td>
                            <h5>{{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5>Detail</h5>
                        </td>
                        <td>
                            <h5>&nbsp;:&nbsp;</h5>
                        </td>
                        <td>
                            <h5>{{ $letter->detail ? $letter->detail : '-' }}</h5>
                        </td>
                    </tr>
                </table>

                <div class="qr-code py-3 py-md-0">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/{{ $letter->no_letter }}&size=100x100"
                        alt="QR Code">
                </div>
            </div>
        </div>
    </div>

    @if (!empty($letter->documents) && $letter->documents->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">File</th>
                                <th>Name</th>
                                <th class="text-center">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($letter->documents->where('status', 'active') as $document)
                                <tr class="align-middle">
                                    <td>
                                        <div class="position-relative d-flex justify-content-center">
                                            <div class="image d-flex justify-content-center align-items-center">
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
                                    <td>{{ $document->file }}</td>
                                    <td>
                                        <div class="actions d-flex justify-content-center align-items-center">
                                            <a href="{{ asset('storage/documents/' . $document->file) }}"
                                                download="{{ $document->file }}"
                                                class="btn btn-primary d-flex align-items-center justify-content-center">
                                                <i class="bx bx-arrow-to-bottom fs-4 p-0 m-0"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if (!empty($letter->content))
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-end w-100">
                    <a href="{{ route('print.letter', $letter->no_letter) }}"
                        class="btn btn-primary d-flex align-items-center gap-1" target="_blank">
                        <i class="bx bx-printer"></i>
                        Print
                    </a>
                </div>
                <div class="table-responsive">
                    <div class="letter">
                        <div class="letter-header d-flex justify-content-between align-items-center gap-2">
                            <div class="logo">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 120px">
                            </div>
                            <div class="letter-header-title d-flex flex-column align-items-center">
                                <h4 class="fw-bold">CV PUTRA PANGGIL JAYA</h4>
                                <span class="fw-bold text-center">Jalan Kolonel Sugiono No.15 Slawi Kulon, Slawi</span>
                                <span class="fw-bold">Email : ppj.slawi.pos@gmail.com</span>
                            </div>
                            <div class="logo opacity-0">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 120px">
                            </div>
                        </div>
                        <hr class="bg-secondary border-2">
                        <div class="letter-body">
                            <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                                <span class="fw-bold"><u>SURAT
                                        {{ strtoupper($letter->archive->category->name) }}</u></span>
                                <span>No: {{ $letter->letter_code }}</span>
                            </div>
                            {!! $letter->content !!}
                        </div>
                        <div class="letter-footer d-flex align-items-center justify-content-between mt-4">
                            <div class="print-border" style="width: max-content;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/{{ $letter->no_letter }}&size=100x100"
                                    alt="QR Code">
                            </div>
                            <div class="d-flex flex-column aliign-items-start">
                                <span>Slawi,
                                    {{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                <span>Mengetahui,</span>
                                <span>Pimpinan CV Putra Panggil Jaya</span>
                                <div class="ttd" style="height: 70px;">

                                </div>
                                <span>Yugie Hermawan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($letter_reply->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Surat Balasan</h4>
                <hr class="bg-secondary">
                <div
                    class="actions d-flex flex-column flex-md-row gap-2 justify-content-between align-items-center mb-3 w-100">
                    <div class="print-select d-flex flex-column flex-md-row align-items-center gap-2">
                        <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                            id="printSelectLetter">
                            <i class='bx bx-receipt'></i>
                            Print surat
                        </a>
                    </div>

                    <a href="{{ route('letter.reply', $letter->no_letter) }}"
                        class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="bx bxs-share bx-flip-horizontal fs-4"></i>
                        Balas lagi
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="myDataTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <input type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th>No Surat</th>
                                <th class="text-nowrap">Kode Surat</th>
                                <th class="text-nowrap">Nama Surat</th>
                                <th>Tanggal</th>
                                <th class="text-center">Author</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($letter_reply->where('status', '!=', 'delete') as $item)
                                <tr class="align-middle">
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center me-3">
                                            <input type="checkbox" value="{{ $item->no_letter }}">
                                        </div>
                                    </td>
                                    <td>{{ $item->no_letter }}</td>
                                    <td>{{ $item->letter_code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->date)->locale('id')->translatedFormat('d F Y') }}
                                    </td>
                                    <td>
                                        <div class="author">
                                            <div class="avatar">
                                                @if (!empty($item->user->avatar))
                                                    <img class="img"
                                                        src="{{ asset('storage/avatars/' . $item->user->avatar) }}">
                                                @else
                                                    <img class="img"
                                                        src="https://ui-avatars.com/api/?background=random&name={{ urlencode($item->user->name) }}">
                                                @endif
                                            </div>
                                            <span>{{ $item->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <span
                                                class="badge {{ $item->status == 'approve' ? 'bg-success text-light' : 'bg-warning text-dark' }} me-3 status-badge">{{ $item->status }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions d-flex justify-content-center">
                                            <div class="dropdown">
                                                <i class="bx bx-cog fs-4" id="action-{{ $item->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="cursor: pointer;" title="Actions"></i>

                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="action-{{ $item->id }}">
                                                    <li>
                                                        <div
                                                            class="d-flex justify-content-center text-center mb-1 fw-bold">
                                                            {{ $item->no_letter }}
                                                        </div>
                                                    </li>

                                                    <hr class="dropdown-divider py-0 my-0">
                                                    @if (!empty($item->content))
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-1"
                                                                href="{{ route('print.letter', $item->no_letter) }}"
                                                                target="_blank">
                                                                <i class='bx bx-printer fs-5'></i>
                                                                Print
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                                            href="{{ route('letter.show', $item->no_letter) }}">
                                                            <i class='bx bx-show fs-5'></i>
                                                            Lihat detail
                                                        </a>
                                                    </li>
                                                    @if (Auth::user()->roles === 'superadmin' || $item->status !== 'approve')
                                                        @if (Auth::user()->roles !== 'user' || (Auth::user()->id === $item->user_id && $item->status !== 'approve'))
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1"
                                                                    href="{{ route($archive->category->name === 'Memo' || $archive->category->name === 'Notulen' ? 'memo.edit' : 'letter.edit', $item->no_letter) }}">
                                                                    <i class='bx bx-pencil fs-5'></i>
                                                                    Edit data
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form id="deleteLetterForm-{{ $item->id }}"
                                                                    action="{{ route('letter.delete', $item->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf @method('PUT')
        
                                                                    <input type="hidden" class="form-control"
                                                                        id="status" name="status" value="delete">
        
                                                                    <a style="cursor: pointer;"
                                                                        class="dropdown-item d-flex align-items-center gap-1"
                                                                        onclick="confirmDeleteLetter('{{ $item->id }}', '{{ $item->no_letter }}', '{{ $item->name }}')">
                                                                        <i class='bx bx-trash fs-5'></i>
                                                                        Hapus
                                                                    </a>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @foreach ($letter_reply as $letter)
                    @if (!empty($letter->content) && $letter->status !== 'delete')
                        <hr class="bg-secondary">
                        <div class="d-flex justify-content-end w-100">
                            <a href="{{ route('print.letter', $letter->no_letter) }}"
                                class="btn btn-primary d-flex align-items-center gap-1" target="_blank">
                                <i class="bx bx-printer"></i>
                                Print
                            </a>
                        </div>
                        <div class="letter">
                            <div class="letter-header d-flex justify-content-between align-items-center gap-2">
                                <div class="logo">
                                    <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo"
                                        style="width: 120px">
                                </div>
                                <div class="letter-header-title d-flex flex-column align-items-center">
                                    <h4 class="fw-bold">CV PUTRA PANGGIL JAYA</h4>
                                    <span class="fw-bold text-center">Jalan Kolonel Sugiono No.15 Slawi Kulon,
                                        Slawi</span>
                                    <span class="fw-bold">Email : ppj.slawi.pos@gmail.com</span>
                                </div>
                                <div class="logo opacity-0">
                                    <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo"
                                        style="width: 120px">
                                </div>
                            </div>
                            <hr class="bg-secondary border-2">
                            <div class="letter-body">
                                <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                                    <span class="fw-bold"><u>SURAT
                                            {{ strtoupper($letter->archive->category->name) }}</u></span>
                                    <span>No: {{ $letter->letter_code }}</span>
                                </div>
                                {!! $letter->content !!}
                            </div>
                            <div class="letter-footer d-flex align-items-center justify-content-between mt-4">
                                <div class="print-border" style="width: max-content;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/{{ $letter->no_letter }}&size=100x100"
                                        alt="QR Code">
                                </div>
                                <div class="d-flex flex-column aliign-items-start">
                                    <span>Slawi,
                                        {{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                    <span>Mengetahui,</span>
                                    <span>Pimpinan CV Putra Panggil Jaya</span>
                                    <div class="ttd" style="height: 70px;">

                                    </div>
                                    <span>Yugie Hermawan</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        function confirmDeleteLetter(letterId, noLetter, letterName) {
            Swal.fire({
                icon: 'question',
                title: 'Anda Yakin?',
                html: `Apakah Anda yakin ingin menghapus <b class="text-danger">${noLetter} - ${letterName}</b>?`,
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
                    document.getElementById('deleteLetterForm-' + letterId).submit();
                }
            });
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkAll = document.getElementById("checkAll");
            const checkboxes = document.querySelectorAll("tbody input[type='checkbox']");
            const printSelectLetter = document.getElementById("printSelectLetter");

            function getSelectedCodes() {
                let selectedCodes = [];
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedCodes.push(checkbox.value);
                    }
                });
                return selectedCodes.join("-");
            }

            checkAll.addEventListener("change", function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
            });

            printSelectLetter.addEventListener("click", function(e) {
                e.preventDefault();
                let selectedCodes = getSelectedCodes();
                if (selectedCodes) {
                    window.location.href = `/print/${selectedCodes}`;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Setidaknya pilih satu untuk dicetak!',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-primary bg-primary border-0 shadow-none',
                        },
                    });
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("figure.table").forEach(function(figure) {
                figure.classList.replace("table", "ck-table");
            });
        });
    </script>
@endpush
