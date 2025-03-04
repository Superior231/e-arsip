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
    <div class="history-icon mb-2 d-none" onclick="historyArchive()">
        <div class="d-flex align-items-center">
            <i class='bx bx-chevrons-right fs-3' id="iconHistory"></i>
            <span class="my-0 py-0">History</span>
        </div>
    </div>

    <div class="actions d-flex flex-column flex-md-row align-items-center justify-content-between gap-2"
        id="actionsContainer">
        <div class="print d-flex align-items-center gap-2 w-100">
            <a href="{{ route('print.archive', implode('-', $archives->pluck('archive_id')->toArray())) }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                target="_blank">
                <i class='bx bx-printer'></i>
                Print all list
            </a>
            <a href="{{ route('print.barcode.archive', implode('-', $archives->pluck('archive_id')->toArray())) }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                target="_blank">
                <i class='bx bx-barcode'></i>
                Print all barcode
            </a>
        </div>

        <div class="print-select d-flex align-items-center justify-content-lg-end gap-2 w-100">
            <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                id="printSelectArchiveList">
                <i class='bx bx-printer'></i>
                Print select list
            </a>
            <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                id="printSelectBarcodeArchiveList">
                <i class='bx bx-barcode'></i>
                Print select barcode
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-lg-2 g-3 mt-0" id="archiveContainer">
        <div class="col col-12 col-lg-8" id="archiveList">
            <div class="card p-4 pt-3">
                <div class="actions d-flex align-items-center justify-content-between">
                    <h4 class="fw-semibold py-0 my-0">{{ $tableTitle }}</h4>
                    <a href="{{ route('archive.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class='bx bx-plus'></i>
                        Arsip
                    </a>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="myDataTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>
                                    <div class="d-flex justify-content-center align-items-center" style="max-width: 30px;">
                                        <input type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th class="text-center">Foto</th>
                                <th class="text-nowrap">ID Arsip</th>
                                <th class="text-nowrap">Nama Arsip</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($archives as $archive)
                                <tr class="align-middle">
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center me-3">
                                            <input type="checkbox" value="{{ $archive->archive_id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="position-relative d-flex justify-content-center me-3">
                                            <div class="image d-flex justify-content-center" style="cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#showArchiveImageModal"
                                                onclick="showImage('{{ $archive->image }}')">
                                                @if (!empty($archive->image))
                                                    <img src="{{ asset('storage/archive/' . $archive->image) }}"
                                                        alt="gambar">
                                                @else
                                                    <img src="{{ url('assets/img/logo_ppj.png') }}" alt="gambar">
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-decoration-none"
                                            style="cursor: pointer;" title="Detail Arsip" data-bs-toggle="modal"
                                            data-bs-target="#detailArchiveModal"
                                            onclick="showDetailArchive(
                                            '{{ $archive->division->name }}',
                                            '{{ $archive->division->place }}',
                                            '{{ $archive->category->name }}',
                                            '{{ $archive->archive_id }}',
                                            '{{ $archive->archive_code }}',
                                            '{{ $archive->name }}',
                                            '{{ $archive->status }}',
                                            '{{ $archive->detail }}',
                                            '{{ $archive->date }}'
                                        )">
                                            <span class="py-0 my-0 text-primary">{{ $archive->archive_id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $archive->name }}</span>
                                            @if ($archive->letters->isNotEmpty())
                                                <a class="text-decoration-underline" style="cursor: pointer;"
                                                    id="showItem-{{ $archive->id }}"
                                                    onclick="showItems({{ $archive->id }})">
                                                    <span>Lihat items</span>
                                                </a>

                                                <div class="show-items-container d-none flex-column"
                                                    id="showItemContainer-{{ $archive->id }}">
                                                    @foreach ($archive->letters as $letter)
                                                        <span class="py-0 my-0">- {{ $letter->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span>{{ $archive->category->name }}</span>
                                    </td>
                                    <td>
                                        <span class="d-none">{{ $archive->date }}</span>
                                        <span>
                                            {{ \Carbon\Carbon::parse($archive->date)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <span
                                            class="badge {{ $archive->status == 'approve' ? 'bg-success text-light' : ($archive->status == 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ $archive->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions d-flex justify-content-center pe-3">
                                            <div class="dropdown">
                                                <i class="bx bx-cog fs-4" id="action-{{ $archive->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"
                                                    title="Actions"></i>

                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="action-{{ $archive->id }}">
                                                    <li>
                                                        <div
                                                            class="d-flex justify-content-center text-center mb-1 fw-bold">
                                                            {{ $archive->archive_id }}
                                                        </div>
                                                    </li>

                                                    <hr class="dropdown-divider py-0 my-0">

                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                                            href="{{ route('print.archive', $archive->archive_id) }}" target="_blank">
                                                            <i class='bx bx-printer fs-5'></i>
                                                            Print
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                                            href="{{ route('archive.show', $archive->archive_id) }}">
                                                            <i class='bx bx-show fs-5'></i>
                                                            Lihat detail
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                                            href="{{ route('letter.create', $archive->archive_id) }}">
                                                            <i class='bx bx-plus fs-5'></i>
                                                            Tambah surat
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                                            href="{{ route('archive.edit', $archive->archive_id) }}">
                                                            <i class='bx bx-pencil fs-5'></i>
                                                            Edit data
                                                        </a>
                                                    </li>
                                                    @if ($archive->status !== 'approve')
                                                        <li>
                                                            <form id="deleteArchiveForm-{{ $archive->id }}"
                                                                action="{{ route('archive.destroy', $archive->id) }}"
                                                                method="post" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')

                                                                <a style="cursor: pointer;"
                                                                    class="dropdown-item d-flex align-items-center gap-1"
                                                                    onclick="confirmDeleteArchive('{{ $archive->id }}', '{{ $archive->archive_id }}', '{{ $archive->name }}')">
                                                                    <i class='bx bx-trash fs-5'></i>
                                                                    Hapus
                                                                </a>
                                                            </form>
                                                        </li>
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
            </div>
        </div>

        <div class="col col-12 col-lg-4" id="historyArchive">
            <div class="card">
                <div class="card-body px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-semibold my-0 py-0 px-3">Riwayat Arsip</h4>
                        <i class='bx bx-chevrons-right fs-3 px-3' id="iconHistory" onclick="historyArchive()"
                            style="cursor: pointer;"></i>
                    </div>
                    <hr class="pb-0 mb-0">
                    <div class="d-flex flex-column" style="max-height: 500px; overflow-y: scroll">
                        @forelse ($history_archive as $history)
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
                                <span class="fs-7">{{ $history->created_at->format('d M Y H:i') }}
                                    WIB</span>
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
    <div class="modal fade" id="detailArchiveModal" tabindex="-1" aria-labelledby="detailArchiveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detailArchiveModalLabel">Detail Arsip</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <header class="mb-0 mb-md-4">
                        <div class="logo d-flex justify-content-center">
                            <img src="{{ url('assets/img/logo_ppj.png') }}" alt="image preview" id="image-preview"
                                style="width: 120px;">
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5 class="fw-bold">CV. Putra Panggil Jaya</h5>
                            <h5 class="fw-bold text-center">Jl. Kolonel Sugiono No. 15 Slawi - Tegal</h5>
                            <h5 class="fw-bold">(0283) 492131-4561266</h5>
                        </div>
                    </header>

                    <div
                        class="d-flex flex-column-reverse flex-md-row justify-content-between align-items-center align-items-md-start gap-2">
                        <table>
                            <tr>
                                <td>
                                    <h5>Status</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5><span id="detailArchiveStatus" class="badge fs-8"></span></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Arsip ID</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailArchiveId"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Kode Arsip</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailArchiveCode"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Nama Arsip</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailArchiveName"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Divisi</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailArchiveDivision"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Kategori</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailArchiveCategory"></h5>
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
                                    <h5 id="detailArchiveDate"></h5>
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
                                    <h5 id="detailArchiveDetail"></h5>
                                </td>
                            </tr>
                        </table>

                        <div class="qr-code py-3 py-md-0">
                            <img src="" alt="QR Code" id="detailArchiveQrCode">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="" class="btn btn-primary" id="detailArchiveLink">More Detail</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showArchiveImageModal" tabindex="-1" aria-labelledby="showArchiveImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="modal-content bg-transparent">
                <div class="modal-header border-0">
                    <div class="close d-flex justify-content-end w-100" data-bs-dismiss="modal" aria-label="Close"
                        style="cursor: pointer;">
                        <i class='bx bx-x text-light fs-1'></i>
                    </div>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <div class="archive-image" style="max-width: 100vw;">
                        <img src="" alt="image" id="showArchiveImage" style="width: 100%;">
                    </div>
                </div>
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

        function showItems(inventoryId) {
            var container = document.getElementById('showItemContainer-' + inventoryId);

            container.classList.toggle('d-none');
            container.classList.toggle('d-flex');
        }

        function showImage(archiveImage) {
            let imageUrl = archiveImage ? '{{ asset('storage/archive') }}/' + archiveImage :
                '{{ url('assets/img/logo_ppj.png') }}';
            $('#showArchiveImage').attr('src', imageUrl);
        }

        function showDetailArchive(
            divisionName, divisionPlace, categoryName,
            archiveId, archiveCode, archiveName,
            archiveStatus, archiveDetail, archiveDate) {

            $('#detailArchiveId').text(archiveId);
            $('#detailArchiveCode').text(archiveCode);
            $('#detailArchiveDivision').text(divisionName + ' (' + divisionPlace + ')');
            $('#detailArchiveCategory').text(categoryName);
            $('#detailArchiveName').text(archiveName);
            $('#detailArchiveDate').text(new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(archiveDate)));
            $('#detailArchiveDetail').text(archiveDetail ? archiveDetail : '-');

            // Atur status dengan warna yang sesuai
            let statusClass = archiveStatus === 'approve' ? 'bg-success' :
                archiveStatus === 'pending' ? 'bg-warning text-dark' :
                'bg-secondary';

            $('#detailArchiveStatus').text(archiveStatus).removeClass('bg-success bg-warning text-dark bg-secondary')
                .addClass(statusClass);

            // Atur QR Code untuk arsip
            let archiveQrCode =
                `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent('{{ request()->getHost() }}/print/archive/' + archiveId)}&size=100x100`;
            $('#detailArchiveQrCode').attr('src', archiveQrCode);

            $('#detailArchiveLink').attr('href', `/archive/${archiveId}`);
        }

        function confirmDeleteArchive(archiveId, archiveCode, archiveName) {
            Swal.fire({
                icon: 'question',
                title: 'Anda Yakin?',
                html: `Menghapus Arsip <b class="text-danger">${archiveCode} - ${archiveName}</b> akan menghapus semua data yang terkait dari sistem secara permanen!`,
                showCancelButton: true,
                confirmButtonText: 'Delete',
                customClass: {
                    popup: 'sw-popup',
                    title: 'sw-title',
                    closeButton: 'sw-close',
                    confirmButton: 'btn-danger bg-danger',
                    cancelButton: 'btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteArchiveForm-' + archiveId).submit();
                }
            });
        }

        function historyArchive() {
            let historyDiv = document.getElementById('historyArchive');
            let archiveList = document.getElementById('archiveList');
            let archiveContainer = document.getElementById('archiveContainer');
            let historyIcon = document.querySelector('.history-icon');
            let icon = document.getElementById("iconHistory");
            let actionsContainer = document.getElementById('actionsContainer');

            if (icon.classList.contains("bx-chevrons-right")) {
                icon.classList.remove("bx-chevrons-right");
                icon.classList.add("bx-chevrons-left");
                localStorage.setItem("historyArchiveState", "closed");
            } else {
                icon.classList.remove("bx-chevrons-left");
                icon.classList.add("bx-chevrons-right");
                localStorage.setItem("historyArchiveState", "open");
            }

            if (historyDiv.style.display === 'none') {
                historyDiv.style.display = 'block';
                archiveList.classList.remove('col-lg-12');
                archiveList.classList.add('col-lg-8');
                archiveContainer.classList.remove('row-cols-lg-1');
                archiveContainer.classList.add('row-cols-lg-2');
                historyIcon.classList.remove('d-flex');
                historyIcon.classList.add('d-none');
                actionsContainer.classList.remove('mt-5');
            } else {
                historyDiv.style.display = 'none';
                archiveList.classList.remove('col-lg-8');
                archiveList.classList.add('col-lg-12');
                archiveContainer.classList.remove('row-cols-lg-2');
                archiveContainer.classList.add('row-cols-lg-1');
                historyIcon.classList.remove('d-none');
                historyIcon.classList.add('d-flex');
                actionsContainer.classList.add('mt-5');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const state = localStorage.getItem("historyArchiveState");
            if (state === "closed") {
                historyArchive();
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkAll = document.getElementById("checkAll");
            const checkboxes = document.querySelectorAll("tbody input[type='checkbox']");
            const printSelectArchiveList = document.getElementById("printSelectArchiveList");
            const printSelectBarcodeArchiveList = document.getElementById("printSelectBarcodeArchiveList");

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

            printSelectArchiveList.addEventListener("click", function(e) {
                e.preventDefault();
                let selectedCodes = getSelectedCodes();
                if (selectedCodes) {
                    window.location.href = `/print/archive/${selectedCodes}`;
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

            printSelectBarcodeArchiveList.addEventListener("click", function(e) {
                e.preventDefault();
                let selectedCodes = getSelectedCodes();
                if (selectedCodes) {
                    window.location.href = `/print/barcode/archive/${selectedCodes}`;
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
