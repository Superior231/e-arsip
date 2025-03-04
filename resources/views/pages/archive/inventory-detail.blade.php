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
    <div class="detail-inventory">
        <!-- Breadcrumb -->
        <ol class="breadcrumb py-0 my-0 mb-3">
            <a href="{{ route('archive.index') }}" class="breadcrumb-items">Arsip</a>
            <a href="{{ route('archive.show', $letter->archive->archive_id) }}"
                class="breadcrumb-items">{{ $letter->archive->archive_id }}</a>
            <a href="" class="breadcrumb-items">{{ $letter->no_letter }}</a>
            <a href="" class="breadcrumb-items active">{{ $navTitle }}</a>
        </ol>
        <!-- Breadcrumb End -->

        @if ($inventory)
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
                            {{ $item->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $item->status }}
                                        </span>
                                    </h5>
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
                                    <h5>{{ $item->sub_code }}</h5>
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
                                    <h5>{{ $item->sub_code }}/{{ $inventory->division->name }}/{{ $inventory->division->place }}
                                    </h5>
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
                                    <h5>{{ $item->name }}</h5>
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
                                    <h5>{{ $item->detail ? $item->detail : '-' }}</h5>
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
                                        {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                    </h5>
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
                                    <h5>{{ $item->quantity }}</h5>
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
                                    <h5>{{ 'Rp. ' . number_format($item->price, 0, ',', '.') }}</h5>
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
                                    <h5>{{ 'Rp. ' . number_format($item->price * $item->quantity, 0, ',', '.') }}</h5>
                                </td>
                            </tr>
                        </table>

                        <div class="qr-code py-3 py-md-0">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?data=https://inventory.hikmal-falah.com/print/item/{{ $item->sub_code }}&size=100x100"
                                alt="QR Code">
                        </div>
                    </div>
                </div>
            </div>
        @else
            <p class="text-danger">Inventory tidak ditemukan.</p>
        @endif
    </div>
@endsection
