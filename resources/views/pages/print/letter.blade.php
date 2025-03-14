@extends('layouts.show')

@push('styles')
    <style>
        @media print {
            * {
                font-family: 'Times New Roman', Times, serif !important;
            }

            body {
                background-color: white !important;
            }

            .new-page {
                break-before: page;
                page-break-before: always;
            }

            .letter {
                padding: 0px !important;
                box-shadow: none !important;
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
                margin: 0px !important;
                padding: 0px !important;
                line-height: 1.5 !important;
                white-space: wrap !important;
            }
        }

        .print-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
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

    <div id="areaPrint">
        <div class="print-container">
            @forelse ($letters as $letter)
                @if ($letter->type == 'notulen')
                    <div class="letter new-page">
                        <div class="letter-header d-flex justify-content-between align-items-center gap-2">
                            <div class="logo">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 100px">
                            </div>
                            <div class="letter-header-title d-flex flex-column align-items-center">
                                <h4 class="fw-bold">CV PUTRA PANGGIL JAYA</h4>
                                <span class="fw-bold text-center">Jalan Kolonel Sugiono No.15 Slawi Kulon, Slawi</span>
                                <span class="fw-bold">Email : ppj.slawi.pos@gmail.com</span>
                            </div>
                            <div class="logo opacity-0">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 100px">
                            </div>
                        </div>
                        <div class="break">
                            <hr class="bg-secondary border-2">
                        </div>
                        <div class="letter-body">
                            <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                                <span class="fw-bold"><u>{{ strtoupper($letter->archive->category->name) }}</u></span>
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
                                        <h5>{{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('l, d F Y') }}
                                        </h5>
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
                                {!! nl2br(e($letter->participant)) !!}
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
                        <div class="letter-footer d-flex align-items-center justify-content-between mt-3">
                            <div class="print-border" style="width: max-content;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/letter/{{ $letter->no_letter }}&size=100x100"
                                    alt="QR Code">
                            </div>
                            <div class="d-flex flex-column aliign-items-start">
                                <span>Slawi,
                                    {{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                <span>Mengetahui,</span>
                                <span>{{ $letter->chairman_position }}</span>
                                <div class="ttd" style="height: 50px;">

                                </div>
                                <span>{{ $letter->chairman }}</span>
                            </div>
                        </div>
                    </div>
                @elseif ($letter->type == 'memo')
                <div class="letter new-page">
                        <div class="letter-header d-flex justify-content-between align-items-center gap-2">
                            <div class="logo">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 100px">
                            </div>
                            <div class="letter-header-title d-flex flex-column align-items-center">
                                <h4 class="fw-bold">CV PUTRA PANGGIL JAYA</h4>
                                <span class="fw-bold text-center">Jalan Kolonel Sugiono No.15 Slawi Kulon, Slawi</span>
                                <span class="fw-bold">Email : ppj.slawi.pos@gmail.com</span>
                            </div>
                            <div class="logo opacity-0">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 100px">
                            </div>
                        </div>
                        <div class="break">
                            <hr class="bg-secondary border-2">
                        </div>
                        <div class="letter-body">
                            <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                                <span class="fw-bold"><u>{{ strtoupper($letter->archive->category->name) }}</u></span>
                                <span>No: {{ $letter->letter_code }}</span>
                            </div>
                            <table>
                                <tr>
                                    <td>
                                        <h5>Nama Memo</h5>
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
                                        <h5>{{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('l, d F Y') }}
                                        </h5>
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
                                        <h5>Isi Memo</h5>
                                    </td>
                                    <td>
                                        <h5>&nbsp;:&nbsp;</h5>
                                    </td>
                                </tr>
                            </table>

                            <div class="content">
                                {!! $letter->content !!}
                            </div>
                        </div>
                        <div class="letter-footer d-flex align-items-center justify-content-between mt-3">
                            <div class="print-border" style="width: max-content;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/letter/{{ $letter->no_letter }}&size=100x100"
                                    alt="QR Code">
                            </div>
                            <div class="d-flex flex-column aliign-items-start">
                                <span>Slawi,
                                    {{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                <span>Mengetahui,</span>
                                <span>Pimpinan CV Putra Panggil Jaya</span>
                                <div class="ttd" style="height: 50px;">

                                </div>
                                <span>Yugie Hermawan</span>
                            </div>
                        </div>
                    </div>
                @elseif ($letter->type == 'letter_out')
                    <div class="letter new-page">
                        <div class="letter-header d-flex justify-content-between align-items-center gap-2">
                            <div class="logo">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 100px">
                            </div>
                            <div class="letter-header-title d-flex flex-column align-items-center">
                                <h4 class="fw-bold">CV PUTRA PANGGIL JAYA</h4>
                                <span class="fw-bold text-center">Jalan Kolonel Sugiono No.15 Slawi Kulon, Slawi</span>
                                <span class="fw-bold">Email : ppj.slawi.pos@gmail.com</span>
                            </div>
                            <div class="logo opacity-0">
                                <img src="{{ asset('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 100px">
                            </div>
                        </div>
                        <div class="break">
                            <hr class="bg-secondary border-2">
                        </div>
                        <div class="letter-body">
                            <div class="d-flex flex-column align-items-center justify-content-center mb-4">
                                <span class="fw-bold"><u>SURAT
                                        {{ strtoupper($letter->archive->category->name) }}</u></span>
                                <span>No: {{ $letter->letter_code }}</span>
                            </div>

                            <div class="content mt-2">
                                {!! $letter->content !!}
                            </div>
                        </div>
                        <div class="letter-footer d-flex align-items-center justify-content-between mt-3">
                            <div class="print-border" style="width: max-content;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/letter/{{ $letter->no_letter }}&size=100x100"
                                    alt="QR Code">
                            </div>
                            <div class="d-flex flex-column aliign-items-start">
                                <span>Slawi,
                                    {{ \Carbon\Carbon::parse($letter->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                <span>Mengetahui,</span>
                                <span>Pimpinan CV Putra Panggil Jaya</span>
                                <div class="ttd" style="height: 50px;">

                                </div>
                                <span>Yugie Hermawan</span>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="px-3 pt-2 d-flex justify-content-center align-items-center">
                    <span>Data tidak ada.</span>
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

        const navbar = document.querySelector("nav");
        const classList = ["shadow-sm", "border-bottom", "border-secondary", "bg-light"];
        const handleScroll = () => {
            const action = window.pageYOffset > 0.1 ? 'add' : 'remove';
            if (navbar) navbar.classList[action](...classList);
        };

        window.addEventListener("scroll", handleScroll);
    </script>
@endpush
