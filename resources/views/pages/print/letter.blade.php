@extends('layouts.show')

@push('styles')
    <style>
        body {
            background-color: #fff !important;
        }

        .back:hover span {
            text-decoration: underline !important;
        }

        button.btn {
            background-color: #2D6CDF;
            color: #ffff;
        }

        button.btn:hover {
            background-color: #275bbd;
            color: #ffff;
        }

        @media print {
            .new-page {
                break-before: page;
                page-break-before: always;
            }
        }

        .print-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .print-border,
        .print-body {
            border: 1px solid black;
            padding: 10px;
            margin: 10px;
            background-color: #ffffff;
            color: #000000;
            font-family: Arial, sans-serif;
        }

        .print-text-bold {
            font-weight: 600;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo-ppj {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        h2,
        h4 {
            padding: 0;
            margin: 0;
        }

        p {
            font-size: clamp(12px, 3vw, 15px) !important;
        }

        .empty-item-container {
            display: flex;
            justify-content: center;
        }

        .empty-item {
            text-align: center !important;
            padding: 10px;
        }

        .information {
            margin-bottom: 10px;
        }

        .information table tr td {
            border: none !important;
            padding: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
@endpush

@section('content')
    <nav class="sticky-top container-fluid d-flex justify-content-between py-3 px-2 px-md-5 W-100">
        <a href="{{ route('index') }}" class="back d-flex align-items-center text-decoration-none text-dark fw-bold gap-1">
            <i class="bx bx-chevron-left fs-3 fw-bold"></i>
            <span>Back to Home</span>
        </a>
        <button type="button" class="btn btn-light d-flex align-items-center gap-1" onclick="printArea('areaPrint')">
            <i class="bx bx-printer"></i>
            Print
        </button>
    </nav>

    <div id="areaPrint">
        <div class="print-container">
            <div class="print-body new-page" style="width:21.5cm;background: #fff; height: min-content;">
                <header>
                    <div class="logo-ppj">
                        <img src="{{ url('assets/img/logo_ppj.png') }}" alt="image preview" style="width: 50px;">
                        <h4>Putra Panggil Jaya</h4>
                    </div>

                    <div class="print-border">
                        <h4 class="print-text-bold">DETAIL ARSIP</h4>
                    </div>
                </header>

                <div style="margin: 10px;">
                    <table>
                        <thead>
                            <tr>
                                <th>ARSIP</th>
                                <th>KODE SURAT</th>
                                <th>NAMA</th>
                                <th>STATUS</th>
                                <th>TANGGAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($letters as $letter)
                                <tr>
                                    <td>{{ $letter->archive->name }}</td>
                                    <td>{{ $letter->letter_code }}</td>
                                    <td>{{ $letter->name }}</td>
                                    <td>{{ $letter->status }}</td>
                                    <td>{{ \Carbon\Carbon::parse($letter->date)->locale('id_ID')->isoFormat('d MMMM Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="new-page" style="width:max-content;background: #fff; height: auto;">
                @if ($letters->count() > 0)
                    @foreach ($letters as $letter)
                        <div class="print-border d-flex p-0">
                            <div class="print-border" style="width: max-content;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/letter/{{ $letter->no_letter }}&size=100x100"
                                    alt="QR Code">
                            </div>

                            <div class="print-border fw-semibold ms-0" style="height: min-content;">
                                Kode Surat:
                                {{ $letter->letter_code }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function printArea(areaId) {
            var printContents = document.getElementById(areaId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }

        const navbar = document.querySelector("nav");
        const classList = ["shadow-sm", "border-bottom", "border-secondary", "bg-light"];
        const handleScroll = () => {
            const action = window.pageYOffset > 0.1 ? 'add' : 'remove';
            if (navbar) navbar.classList[action](...classList);
        };

        window.addEventListener("scroll", handleScroll);
    </script>
@endpush
