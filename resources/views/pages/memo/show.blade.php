@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        table tr td>* {
            margin: 0px !important;
            padding: 0px !important;
        }
        .letter-body table tr td>* {
            margin: 0px !important;
            padding: 2px !important;
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
                <table class="d-flex align-items-center">
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
                            {{ $letter->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
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
                            <h5>{{ $letter->archive->category->name }}</h5>
                        </td>
                    </tr>
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
                    @if (!empty($letter->detail))
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
                    @endif
                </table>

                <div class="qr-code py-3 py-md-0">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/letter/{{ $letter->no_letter }}&size=100x100"
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
                                <th>Nama Dokumen</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($letter->documents->where('status', 'active') as $document)
                                <tr class="align-middle">
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

    @if ($letter->type === 'notulen')
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-end w-100">
                    <a href="{{ route('memo.content', $letter->no_letter) }}"
                        class="btn btn-primary d-flex align-items-center gap-1" target="_blank">
                        <i class="bx bx-printer"></i>
                        Print
                    </a>
                </div>
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
                            <span class="fw-bold"><u>SURAT {{ strtoupper($letter->archive->category->name) }}</u></span>
                            <span>No: {{ $letter->letter_code }}</span>
                        </div>
                        <table>
                            <tr>
                                <td>
                                    <h5>Nama Rapat</h5>
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
                                    <h5>Hari, Tanggal</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5>{{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('l, d F Y') }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Waktu</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5>{{ $letter->start_time }} - {{ $letter->end_time }} WIB</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Tempat</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5>{{ $letter->place }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Acara</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5>{{ $letter->event }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Pimpinan Rapat</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5>{{ $letter->chairman }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Notulis</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5>{{ $letter->notulis }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Partisipan</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                            </tr>
                        </table>

                        <div class="mt-2">
                            {!! $letter->participant !!}
                        </div>
                        
                        <div class="content mt-2">
                            <table class="mb-2">
                                <tr>
                                    <td>
                                        <h5>Isi Rapat</h5>
                                    </td>
                                    <td>
                                        <h5>&nbsp;:&nbsp;</h5>
                                    </td>
                            </table>
                            {!! $letter->content !!}
                            <table class="mb-2">
                                <tr>
                                    <td>
                                        <h5>Hasil Rapat</h5>
                                    </td>
                                    <td>
                                        <h5>&nbsp;:&nbsp;</h5>
                                    </td>
                            </table>
                            {!! $letter->decision !!}
                        </div>
                    </div>
                    <div class="letter-footer d-flex justify-content-end mt-4">
                        <div class="d-flex flex-column aliign-items-start">
                            <span>Slawi,
                                {{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</span>
                            <span>Mengetahui,</span>
                            <span>{{ $letter->chairman_position }}</span>
                            <div class="ttd" style="height: 70px;">

                            </div>
                            <span>{{ $letter->chairman }}</span>
                        </div>
                    </div>
                </div>
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
            const printSelectLetterList = document.getElementById("printSelectLetterList");
            const printSelectBarcodeLetterList = document.getElementById("printSelectBarcodeLetterList");

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

            printSelectLetterList.addEventListener("click", function(e) {
                e.preventDefault();
                let selectedCodes = getSelectedCodes();
                if (selectedCodes) {
                    window.location.href = `/print/letter/${selectedCodes}`;
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

            printSelectBarcodeLetterList.addEventListener("click", function(e) {
                e.preventDefault();
                let selectedCodes = getSelectedCodes();
                if (selectedCodes) {
                    window.location.href = `/print/barcode/letter/${selectedCodes}`;
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
@endpush
