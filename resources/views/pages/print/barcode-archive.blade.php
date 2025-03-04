@extends('layouts.show')

@push('styles')
    <style>
        body {
            background-color: #ffff !important;
        }

        .back:hover span {
            text-decoration: underline !important;
        }

        @media print {
            .new-page {
                break-before: page;
                page-break-before: always;
            }
        }

        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
        .barcode {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            max-width: 150px;
            flex: 1 1 150px;
            min-width: 150px;
            margin-top: 10px;
            padding: 10px;
            border: 2px solid black !important;
            
        }
        .barcode .title {
            display: flex;
            justify-content: center;
        }
        .barcode span {
            text-align: center !important;
            font-size: clamp(10px, 3vw, 13px) !important;
        }
        button.btn {
            background-color: #2D6CDF;
            color: #ffff;
        }
        button.btn:hover {
            background-color: #275bbd;
            color: #ffff;
        }
    </style>
@endpush

@section('content')
    <nav class="sticky-top container-fluid d-flex justify-content-between py-3 px-2 px-md-5 W-100 mb-3">
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
        <div class="barcode-container">
            @forelse ($archives as $archive)
                <div class="barcode">
                    <div class="img">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/archive/{{ $archive->archive_id }}&size=100x100" alt="QR Code">
                    </div>
                    <div class="title">
                        <span>{{ $archive->archive_code }} - {{ $archive->name }}</span>
                    </div>
                </div>
            @empty
                <div class="d-flex align-items-center justify-content-center w-100">
                    <span>Arsip tidak ada.</span>
                </div>
            @endforelse
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
    </script>
@endpush
