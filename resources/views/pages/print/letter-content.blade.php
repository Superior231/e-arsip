@extends('layouts.show')

@push('styles')
    <style>
        @media print {
            * {
                font-family: 'Times New Roman', Times, serif !important;
            }

            body {
                background-color: white !important;
                ;
            }

            .letter-header,
            .break,
            .letter-body,
            .letter-footer {
                padding: 0px 40px !important;
            }

            .letter-body p,
            .letter-body span {
                margin-bottom: 0px !important;
                line-height: 1.5 !important;
                white-space: wrap !important;
            }
        }
    </style>
@endpush

@section('content')
    <nav class="sticky-top container-fluid d-flex justify-content-between py-3 px-2 px-md-5 W-100">
        <a href="{{ route('index') }}" class="back d-flex align-items-center text-decoration-none text-dark fw-bold gap-1">
            <i class="bx bx-chevron-left fs-3 fw-bold"></i>
            <span>Back to Home</span>
        </a>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-1" onclick="printArea('areaPrint')">
            <i class="bx bx-printer"></i>
            Print
        </button>
    </nav>

    <div class="letter" id="areaPrint">
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
        <div class="break">
            <hr class="bg-secondary border-2">
        </div>
        <div class="letter-body">
            <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                <span class="fw-bold"><u>SURAT {{ strtoupper($letter->archive->category->name) }}</u></span>
                <span>No: {{ $letter->letter_code }}</span>
            </div>
            {!! $letter->content !!}
        </div>
        <div class="letter-footer d-flex justify-content-end mt-4">
            <div class="d-flex flex-column aliign-items-start">
                <span>Slawi, {{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</span>
                <span>Mengetahui,</span>
                <span>Pimpinan CV Putra Panggil Jaya</span>
                <div class="ttd" style="height: 70px;">

                </div>
                <span>Yugie Hermawan</span>
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
