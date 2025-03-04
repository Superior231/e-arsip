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
                            <h5>{{ \Carbon\Carbon::parse($letter->date)->locale('id_ID')->isoFormat('d MMMM Y') }}</h5>
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
@endsection
