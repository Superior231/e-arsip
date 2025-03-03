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
<div class="detail-archive">
        <!-- Breadcrumb -->
        <ol class="breadcrumb py-0 my-0 mb-3">
            <a href="{{ route('archive.index') }}" class="breadcrumb-items">Arsip</a>
            <a href="" class="breadcrumb-items active">{{ $archive->archive_id }}</a>
        </ol>
        <!-- Breadcrumb End -->

        <div class="history-icon mb-2 d-none" onclick="historyLetter()">
            <div class="d-flex align-items-center">
                <i class='bx bx-chevrons-right fs-3' id="iconHistory"></i>
                <span class="my-0 py-0">History</span>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-lg-2 g-3" id="letterContainer">
            <div class="col col-12 col-lg-8" id="letterList">
                <div class="card">
                    <div class="card-body">
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

                        <div
                            class="d-flex flex-column-reverse flex-md-row justify-content-between align-items-center gap-2">
                            <table>
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
                            {{ $archive->status == 'approve' ? 'bg-success' : ($archive->status == 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                {{ $archive->status }}
                                            </span>
                                        </h5>
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
                                        <h5>{{ $archive->archive_id }}</h5>
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
                                        <h5>{{ $archive->archive_code }}</h5>
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
                                        <h5>{{ $archive->name }}</h5>
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
                                        <h5>{{ $archive->division->name }} ({{ $archive->division->place }})</h5>
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
                                        <h5>{{ $archive->category->name }}</h5>
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
                                        <h5>
                                            {{ \Carbon\Carbon::parse($archive->date)->format('d M Y') }}
                                        </h5>
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
                                        <h5>{{ $archive->detail ? $archive->detail : '-' }}</h5>
                                    </td>
                                </tr>
                            </table>

                            <div class="qr-code py-3 py-md-0">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/archive/{{ $archive->archive_id }}&size=100x100"
                                    alt="QR Code">
                            </div>
                        </div>

                        <hr class="mb-4">

                        <div
                            class="actions d-flex flex-column flex-md-row gap-2 justify-content-between align-items-center mb-3 w-100">
                            <div class="print-select d-flex flex-column flex-md-row align-items-center gap-2">
                                <a href="#"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                                    id="printSelectLetterList">
                                    <i class='bx bx-printer'></i>
                                    Print select list
                                </a>
                                <a href="#"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                                    id="printSelectBarcodeLetterList">
                                    <i class='bx bx-barcode'></i>
                                    Print select barcode
                                </a>
                            </div>

                            <a href="#"
                                class="btn btn-primary d-flex align-items-center gap-1">
                                <i class='bx bx-plus fs-5'></i>
                                <span class="my-0 py-0">Tambah surat</span>
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table id="myDataTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex justify-content-end align-items-center" style="width: 30px;">
                                                <input type="checkbox" id="checkAll">
                                            </div>
                                        </th>
                                        <th class="text-center">Foto</th>
                                        <th class="text-nowrap">No Surat</th>
                                        <th class="text-nowrap">Kode Surat</th>
                                        <th class="text-nowrap" style="min-width: 200px;">Nama Surat</th>
                                        <th>Tanggal</th>
                                        <th class="text-center w-100">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($archive->letters as $letter)
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex justify-content-center align-items-center me-3">
                                                    <input type="checkbox" value="{{ $letter->sub_code }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="position-relative d-flex justify-content-center">
                                                    <div class="image d-flex justify-content-center"
                                                        style="cursor: pointer;" data-bs-toggle="modal"
                                                        data-bs-target="#showImageModal"
                                                        onclick="showImage('{{ $letter->image }}')">
                                                        @if (!empty($letter->image))
                                                            <img src="{{ asset('storage/letter/' . $letter->image) }}"
                                                                alt="gambar" class="img-fluid">
                                                        @else
                                                            <img src="{{ url('assets/img/logo_ppj.png') }}" alt="gambar"
                                                                class="img-fluid">
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="link-primary"
                                                    style="cursor: pointer;" title="Detail Surat"
                                                    data-bs-toggle="modal" data-bs-target="#detailLetterModal"
                                                    onclick="showDetailLetter(
                                                    '{{ $letter->status }}',
                                                    '{{ $letter->archive->division->name }}',
                                                    '{{ $letter->archive->division->place }}',
                                                    '{{ $letter->name }}',
                                                    '{{ $letter->detail }}',
                                                    '{{ $letter->date }}',
                                                )">
                                                    {{ $letter->no_letter }}
                                                </span>
                                            </td>
                                            <td><span>{{ $letter->name }}</span></td>
                                            <td>
                                                <span class="d-none">{{ $letter->date }}</span>
                                                <span>
                                                    {{ \Carbon\Carbon::parse($letter->date)->format('d M Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <span
                                                        class="badge {{ $letter->status == 'active' ? 'bg-success text-light' : 'bg-warning text-dark' }} me-3">{{ $letter->status }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions d-flex justify-content-center">
                                                    <div class="dropdown pe-3">
                                                        <i class="bx bx-cog fs-4" id="action-{{ $letter->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false"
                                                            style="cursor: pointer;" title="Actions"></i>

                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="action-{{ $letter->id }}">
                                                            <li>
                                                                <div class="d-flex justify-content-center mb-1 fw-bold">
                                                                    {{ $letter->name }}
                                                                </div>
                                                            </li>

                                                            <hr class="dropdown-divider py-0 my-0">

                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1"
                                                                    href="#"
                                                                    target="_blank">
                                                                    <i class='bx bx-printer fs-5'></i>
                                                                    Print
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1"
                                                                    href="#">
                                                                    <i class='bx bx-show fs-5'></i>
                                                                    Lihat detail
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1"
                                                                    href="#">
                                                                    <i class='bx bx-pencil fs-5'></i>
                                                                    Edit data
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form id="deleteItemForm-{{ $letter->id }}"
                                                                    action="#"
                                                                    method="post" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <a href="#"
                                                                        class="dropdown-item d-flex align-items-center gap-1"
                                                                        onclick="confirmDeleteLetter('{{ $letter->id }}', '{{ $letter->sub_code }}', '{{ $letter->name }}')">
                                                                        <i class='bx bx-trash fs-5'></i>
                                                                        Hapus
                                                                    </a>
                                                                </form>
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

            <div class="col col-12 col-lg-4" id="historyLetter">
                <div class="card">
                    <div class="card-body px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-semibold my-0 py-0 px-3">Riwayat Surat</h4>
                            <i class='bx bx-chevrons-right fs-3 px-3' id="iconHistory" onclick="historyLetter()"
                                style="cursor: pointer;"></i>
                        </div>
                        <hr class="pb-0 mb-0">
                        <div class="d-flex flex-column" style="max-height: 500px; overflow-y: scroll">
                            @forelse ($history_letter as $history)
                                @php
                                    $bgClass = match ($history->method) {
                                        'create' => 'bg-success text-light',
                                        'update' => 'bg-warning',
                                        'mutate' => 'bg-primary text-light',
                                        'create, mutate' => 'bg-dark text-light',
                                        'mutate, update' => 'bg-dark text-light',
                                        'mutate, delete' => 'bg-danger text-light',
                                        'mutate, in' => 'bg-primary text-light',
                                        'mutate, out' => 'bg-danger text-light',
                                        'delete' => 'bg-danger text-light',
                                        default => 'bg-secondary text-dark',
                                    };
                                @endphp

                                <div class="notification-header d-flex justify-content-between {{ $bgClass }} px-3">
                                    <span class="fs-7">{{ $history->title }}</span>
                                    <span
                                        class="fs-7">{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i') }}
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
    </div>



    <!-- Modal -->
    <div class="modal fade" id="detailLetterModal" tabindex="-1" aria-labelledby="detailItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detailItemModalLabel">Detail Item</h4>
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
                                    <h5><span id="detailItemStatus" class="badge fs-8"></span></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Kode</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailItemCode"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Kode Aset</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailItemCodeAset"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Nama Item</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailItemName"></h5>
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
                                    <h5 id="detailItemDetail"></h5>
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
                                    <h5 id="detailItemDate"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Quantity</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailItemQuantity"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Harga Satuan</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailItemPrice"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Subtotal</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailItemSubtotal"></h5>
                                </td>
                            </tr>
                        </table>

                        <div class="qr-code py-3 py-md-0">
                            <img src="" alt="QR Code" id="detailItemQrCode">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="" class="btn btn-primary" id="detailItemLink">More detail</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showImageModal" tabindex="-1" aria-labelledby="showImageModalLabel"
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
                    <div class="item-image" style="max-width: 100vw;">
                        <img src="" alt="image" id="showImage" style="width: 100%;">
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

        function showImage(image) {
            let imageUrl = image ? '{{ asset('storage/letter') }}/' + image :
                '{{ url('assets/img/logo_ppj.png') }}';
            $('#showImage').attr('src', imageUrl);
        }

        function showDetailLetter(
            itemStatus, itemCode, divisionName,
            divisionPlace, itemName, itemDetail,
            itemDate, itemQuantity, itemPrice) {
            $('#detailItemStatus').text(itemStatus);
            $('#detailItemCode').text(itemCode);
            $('#detailItemCodeAset').text(`${itemCode}/${divisionName}/${divisionPlace}`);
            $('#detailItemName').text(itemName);
            $('#detailItemDetail').text(itemDetail ? itemDetail : '-');
            $('#detailItemQuantity').text(itemQuantity);
            $('#detailItemPrice').text('Rp. ' + new Intl.NumberFormat('id-ID').format(itemPrice));
            $('#detailItemSubtotal').text('Rp. ' + new Intl.NumberFormat('id-ID').format(itemPrice * itemQuantity));
            $('#detailItemDate').text(new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(itemDate)));

            let statusClass = itemStatus === 'active' ? 'bg-success' : 'bg-warning text-dark';

            $('#detailItemStatus').text(itemStatus).removeClass('bg-success bg-warning text-dark bg-secondary')
                .addClass(statusClass);

            let itemQrCode =
                `https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/item/${itemCode}&size=100x100`;
            $('#detailItemQrCode').attr('src', itemQrCode);

            $('#detailItemLink').attr('href', `/letter/${letterCode}`);
        }

        function confirmDeleteLetter(letterId, letterCode, letterName) {
            Swal.fire({
                icon: 'question',
                title: 'Anda Yakin?',
                html: `Menghapus surat <b class="text-danger">${letterCode} - ${letterName}</b> akan menghapus data dari sistem secara permanen!`,
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

        function historyLetter() {
            let historyDiv = document.getElementById('historyLetter');
            let letterList = document.getElementById('letterList');
            let letterContainer = document.getElementById('letterContainer');
            let historyIcon = document.querySelector('.history-icon');
            let icon = document.getElementById("iconHistory");

            if (icon.classList.contains("bx-chevrons-right")) {
                icon.classList.remove("bx-chevrons-right");
                icon.classList.add("bx-chevrons-left");
                localStorage.setItem("historyLetterState", "closed");
            } else {
                icon.classList.remove("bx-chevrons-left");
                icon.classList.add("bx-chevrons-right");
                localStorage.setItem("historyLetterState", "open");
            }

            if (historyDiv.style.display === 'none') {
                historyDiv.style.display = 'block';
                letterList.classList.remove('col-lg-12');
                letterList.classList.add('col-lg-8');
                letterContainer.classList.remove('row-cols-lg-1');
                letterContainer.classList.add('row-cols-lg-2');
                historyIcon.classList.remove('d-flex');
                historyIcon.classList.add('d-none');
            } else {
                historyDiv.style.display = 'none';
                letterList.classList.remove('col-lg-8');
                letterList.classList.add('col-lg-12');
                letterContainer.classList.remove('row-cols-lg-2');
                letterContainer.classList.add('row-cols-lg-1');
                historyIcon.classList.remove('d-none');
                historyIcon.classList.add('d-flex');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const state = localStorage.getItem("historyLetterState");
            if (state === "closed") {
                historyLetter();
            }
        });
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
