@extends('layouts.main')

@push('styles')
    <style>
        table tr td>* {
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
                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ request()->getHost() }}/print/letter/{{ $letter->no_letter }}&size=100x100"
                        alt="QR Code">
                </div>
            </div>
        </div>
    </div>

    @if (!empty($letter->documents))
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
                            @foreach ($letter->documents as $document)
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

    @if (!empty($letter->content))
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-end w-100">
                    <a href="{{ route('letter.content', $letter->no_letter) }}" class="btn btn-primary d-flex align-items-center gap-1" target="_blank">
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
            </div>
        </div>
    @endif
@endsection
