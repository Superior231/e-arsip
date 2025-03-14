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

        table tr td>* {
            margin: 0px !important;
            padding: 0px !important;
        }
    </style>
@endpush


@section('content')
    <div class="history-icon mb-2 d-none" onclick="historyLetter()">
        <div class="d-flex align-items-center">
            <i class='bx bx-chevrons-right fs-3' id="iconHistory"></i>
            <span class="my-0 py-0">History</span>
        </div>
    </div>

    <div class="actions d-flex flex-column flex-md-row align-items-center justify-content-between gap-2"
        id="actionsContainer">
        <div class="print d-flex align-items-center gap-2 w-100">
            <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                id="printSelectLetter">
                <i class='bx bx-receipt'></i>
                Print surat
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-lg-2 g-3 mt-0" id="letterContainer">
        <div class="col col-12 col-lg-8" id="letterList">
            <div class="card p-4 pt-3">
                <h4 class="fw-semibold py-0 my-0">{{ $tableTitle }}</h4>
                <hr>
                <div class="table-responsive">
                    <table id="myDataTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <input type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th class="text-center">Foto</th>
                                <th class="text-nowrap">No Surat</th>
                                <th class="text-nowrap" style="min-width: 150px;">Kode Surat</th>
                                <th>Arsip</th>
                                <th class="text-nowrap" style="min-width: 200px;">Nama Surat</th>
                                <th>Tanggal</th>
                                <th>Author</th>
                                <th class="text-center w-100">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($letters->where('status', '!=', 'delete') as $letter)
                                <tr class="align-middle">
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center me-3">
                                            <input type="checkbox" value="{{ $letter->no_letter }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="position-relative d-flex justify-content-center">
                                            @if ($letter->documents->where('type', 'image')->where('status', 'active')->isNotEmpty())
                                                <div class="image d-flex justify-content-center" style="cursor: pointer;"
                                                    data-bs-toggle="modal" data-bs-target="#showImageModal"
                                                    onclick="showImages({{ json_encode($letter->documents->where('type', 'image')->where('status', 'active')->pluck('file')->toArray()) }})">
                                                    <img src="{{ asset('storage/documents/' . $letter->documents->where('type', 'image')->where('status', 'active')->first()->file) }}"
                                                        alt="gambar" class="img-fluid">
                                                </div>
                                            @else
                                                <div class="image d-flex justify-content-center" style="cursor: pointer;"
                                                    data-bs-toggle="modal" data-bs-target="#showImageModal"
                                                    onclick="showImages([])">
                                                    <img src="{{ url('assets/img/logo_ppj.png') }}" alt="gambar"
                                                        class="img-fluid">
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="link-primary" style="cursor: pointer;" title="Detail Surat"
                                            data-bs-toggle="modal" data-bs-target="#detailLetterModal"
                                            onclick="showDetailLetter(
                                                    '{{ $letter->user->avatar }}',
                                                    '{{ $letter->user->name }}',
                                                    '{{ $letter->status }}',
                                                    '{{ $letter->type }}',
                                                    '{{ $letter->no_letter }}',
                                                    '{{ $letter->letter_code }}',
                                                    '{{ $letter->archive->name }}',
                                                    '{{ $letter->item ? $letter->item->name : '' }}',
                                                    '{{ $letter->name }}',
                                                    '{{ $letter->date }}',
                                                    '{{ $letter->detail }}',
                                                )">
                                            {{ $letter->no_letter }}
                                        </span>
                                    </td>
                                    <td><span>{{ $letter->letter_code }}</span></td>
                                    <td><span>{{ $letter->archive->name }}</span></td>
                                    <td><span>{{ $letter->name }}</span></td>
                                    <td>
                                        <span class="d-none">{{ $letter->date }}</span>
                                        <span>
                                            {{ \Carbon\Carbon::parse($letter->date)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="author">
                                            <div class="avatar">
                                                @if (!empty($letter->user->avatar))
                                                    <img class="img"
                                                        src="{{ asset('storage/avatars/' . $letter->user->avatar) }}">
                                                @else
                                                    <img class="img"
                                                        src="https://ui-avatars.com/api/?background=random&name={{ urlencode($letter->user->name) }}">
                                                @endif
                                            </div>
                                            <span>{{ $letter->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <span
                                                class="badge {{ $letter->status == 'approve' ? 'bg-success text-light' : 'bg-warning text-dark' }} status-badge me-3">{{ $letter->status }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions d-flex justify-content-center">
                                            <div class="dropdown pe-3">
                                                <i class="bx bx-cog fs-4" id="action-{{ $letter->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"
                                                    title="Actions"></i>

                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="action-{{ $letter->id }}">
                                                    <li>
                                                        <div class="d-flex justify-content-center text-center mb-1 fw-bold">
                                                            {{ $letter->no_letter }}
                                                        </div>
                                                    </li>

                                                    <hr class="dropdown-divider py-0 my-0">

                                                    @if ($letter->type == 'letter_in')
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-1"
                                                                href="{{ route('letter.reply', $letter->no_letter) }}">
                                                                <i class='bx bx-share bx-flip-horizontal fs-5'></i>
                                                                Balas
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if (!empty($letter->content))
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-1"
                                                                href="{{ route('print.letter', $letter->no_letter) }}"
                                                                target="_blank">
                                                                <i class='bx bx-printer fs-5'></i>
                                                                Print
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                                            href="{{ route('letter.show', $letter->no_letter) }}">
                                                            <i class='bx bx-show fs-5'></i>
                                                            Lihat detail
                                                        </a>
                                                    </li>
                                                    @if (Auth::user()->roles === 'superadmin' || $letter->status !== 'approve')
                                                        @if (Auth::user()->roles !== 'user' || (Auth::user()->id === $letter->user_id && $letter->status !== 'approve'))
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1"
                                                                    href="{{ route('letter.edit', $letter->no_letter) }}">
                                                                    <i class='bx bx-pencil fs-5'></i>
                                                                    Edit data
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form id="deleteLetterForm-{{ $letter->id }}"
                                                                    action="{{ route('letter.delete', $letter->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf @method('PUT')
        
                                                                    <input type="hidden" class="form-control" id="status"
                                                                        name="status" value="delete">
        
                                                                    <a style="cursor: pointer;"
                                                                        class="dropdown-item d-flex align-items-center gap-1"
                                                                        onclick="confirmDeleteLetter('{{ $letter->id }}', '{{ $letter->no_letter }}', '{{ $letter->name }}')">
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
            </div>
        </div>

        <div class="col col-12 col-lg-4" id="historyLetter">
            <div class="card">
                <div class="card-body px-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-semibold my-0 py-0 px-3">{{ $historyLetterName }}</h4>
                        <i class='bx bx-chevrons-right fs-3 px-3' id="iconHistory" onclick="historyLetter()"
                            style="cursor: pointer;"></i>
                    </div>
                    <hr class="pb-0 mb-0">
                    <div class="d-flex flex-column" style="max-height: 500px; overflow-y: scroll">
                        @forelse ($historyLetter as $history)
                            <a href="{{ route('history.detail', $history->id) }}" class="text-decoration-none text-dark"
                                style="cursor: pointer;">
                                @php
                                    $bgClass = match ($history->method) {
                                        'create' => 'bg-success text-light',
                                        'update' => 'bg-warning',
                                        'update status' => 'bg-primary text-light',
                                        'update status, update' => 'bg-dark text-light',
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
                            </a>
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


    @if (Auth::user()->roles === 'superadmin')
        <div class="d-none" style="position: fixed; bottom: 20px; right: 20px;" id="approveLetter">
            <form action="{{ route('letter.approve') }}" method="POST" id="approveLetterForm">
                @csrf
                <input type="hidden" name="no_letters" id="selectedCodes">
                <button type="button" id="approveLetterBtn"
                    class="btn btn-success py-2 px-4 rounded-pill d-flex align-items-center justify-content-center gap-1">
                    <i class='bx bx-check-circle'></i>
                    <span>Approve</span>
                </button>
            </form>
        </div>
    @endif


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
                                    <h5>Oleh</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <div class="author py-2">
                                        <div class="avatar">
                                            <img id="detailLetterUserAvatar" class="img" src="">
                                        </div>
                                        <h5 class="ms-4 ps-1 mb-0 pb-0" id="detailLetterUserName"></h5>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Status</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5><span id="detailLetterStatus" class="badge fs-8"></span></h5>
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
                                    <h5 id="detailNoLetter"></h5>
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
                                    <h5 id="detailLetterCode"></h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5>Arsip</h5>
                                </td>
                                <td>
                                    <h5>&nbsp;:&nbsp;</h5>
                                </td>
                                <td>
                                    <h5 id="detailLetterArchive"></h5>
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
                                    <h5 id="detailLetterType"></h5>
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
                                    <h5 id="detailLetterName"></h5>
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
                                    <h5 id="detailLetterDate"></h5>
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
                                    <h5 id="detailLetterDetail"></h5>
                                </td>
                            </tr>
                        </table>

                        <div class="qr-code py-3 py-md-0">
                            <img src="" alt="QR Code" id="detailLetterQrCode">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="" class="btn btn-primary" id="detailLetterLink">More detail</a>
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
                    <div class="item-image d-flex flex-column gap-3" style="max-width: 100%;" id="modalImagesContainer">
                        <img src="">
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

        function showImages(images) {
            let container = document.getElementById("modalImagesContainer");
            container.innerHTML = "";

            if (images.length === 0) {
                container.innerHTML =
                    `
                    <img src="{{ url('assets/img/logo_ppj.png') }}" class="img-fluid" alt="No Image" style="max-width: 100%;">`;
            } else {
                images.forEach(image => {
                    let imageUrl = image ? `{{ asset('storage/documents') }}/${image}` :
                        `{{ url('assets/img/logo_ppj.png') }}`;
                    container.innerHTML += `
                        <img src="${imageUrl}" alt="image">`;
                });
            }
        }

        function showDetailLetter(
            letterUserAvatar, letterUserName,
            letterStatus, letterType, noLetter, letterCode, letterArchive,
            item, letterName, letterDate, letterDetail) {
            var avatarUrl = letterUserAvatar ? '{{ asset('storage/avatars') }}/' + letterUserAvatar :
                `https://ui-avatars.com/api/?background=random&name=${encodeURIComponent(letterUserName)}`;

            $('#detailLetterUserAvatar').attr('src', avatarUrl);
            $('#detailLetterUserName').text(letterUserName);

            $('#detailLetterStatus').text(letterStatus);
            $('#detailNoLetter').text(noLetter);
            $('#detailLetterCode').text(letterCode);
            $('#detailLetterArchive').text(letterArchive);
            $('#detailLetterType').text(letterType === 'letter_in' ? 'Surat Masuk' : 'Surat Keluar');
            $('#detailItem').text(item);
            $('#detailLetterName').text(letterName);
            $('#detailLetterDetail').text(letterDetail ? letterDetail : '-');
            $('#detailLetterDate').text(new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(letterDate)));

            let statusClass = letterStatus === 'active' ? 'bg-success' : 'bg-warning text-dark';

            $('#detailLetterStatus').text(letterStatus).removeClass('bg-success bg-warning text-dark bg-secondary')
                .addClass(statusClass);

            let letterQrCode =
                `https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/${noLetter}&size=100x100`;
            $('#detailLetterQrCode').attr('src', letterQrCode);

            $('#detailLetterLink').attr('href', `/letter/${noLetter}`);
        }

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
                actionsContainer.classList.remove('mt-5');
            } else {
                historyDiv.style.display = 'none';
                letterList.classList.remove('col-lg-8');
                letterList.classList.add('col-lg-12');
                letterContainer.classList.remove('row-cols-lg-2');
                letterContainer.classList.add('row-cols-lg-1');
                historyIcon.classList.remove('d-none');
                historyIcon.classList.add('d-flex');
                actionsContainer.classList.add('mt-5');
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
            const printSelectLetter = document.getElementById("printSelectLetter");
            const approveLetter = document.getElementById("approveLetter");
            const approveLetterForm = document.getElementById("approveLetterForm");
            const approveLetterBtn = document.getElementById("approveLetterBtn");
            const selectedCodesInput = document.getElementById("selectedCodes");

            function getSelectedCodes() {
                let selectedCodes = [];
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedCodes.push(checkbox.value);
                    }
                });
                return selectedCodes.join("-");
            }

            function getSelectedApproveCodes() {
                let selectedCodes = [];
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const row = checkbox.closest("tr");
                        const statusBadge = row.querySelector(".status-badge"); // Ambil status
                        if (statusBadge && statusBadge.textContent.trim().toLowerCase() !== "approve") {
                            selectedCodes.push(checkbox.value);
                        }
                    }
                });
                return selectedCodes;
            }

            function toggleApproveButton() {
                const selectedCodes = getSelectedApproveCodes();
                approveLetter.classList.toggle("d-flex", selectedCodes.length > 0);
                approveLetter.classList.toggle("d-none", selectedCodes.length === 0);
            }

            // Event listener untuk checkAll
            checkAll.addEventListener("change", function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
                toggleApproveButton();
            });

            // Event listener untuk setiap checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", toggleApproveButton);
            });


            @if (Auth::user()->roles === 'superadmin')
                approveLetterBtn.addEventListener("click", function(event) {
                    event.preventDefault();

                    let selectedCodes = getSelectedApproveCodes();
                    if (selectedCodes.length === 0) return;

                    selectedCodesInput.value = selectedCodes.join(",");
                    Swal.fire({
                        icon: 'question',
                        title: 'Anda Yakin?',
                        html: `Apakah Anda yakin ingin menyetujui <b class="text-primary">${selectedCodes.join(", ")}</b>?`,
                        showCancelButton: true,
                        confirmButtonText: 'Approve',
                        customClass: {
                            popup: 'sw-popup',
                            title: 'sw-title',
                            closeButton: 'sw-close',
                            confirmButton: 'btn-success bg-success border-0 shadow-none',
                            cancelButton: 'btn-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            approveLetterForm.submit();
                        }
                    });
                });
            @endif


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
@endpush
