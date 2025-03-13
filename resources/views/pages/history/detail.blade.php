@extends('layouts.main')

@push('styles')
    <style>
        .detail p,
        .detail span {
            margin-bottom: 0px !important;
            line-height: 1.5 !important;
            white-space: wrap !important;
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <ol class="breadcrumb py-0 my-0 mb-3">
        <a href="{{ route('history.index') }}" class="breadcrumb-items">History</a>
        <a href="" class="breadcrumb-items active">{{ $navTitle }}</a>
    </ol>
    <!-- Breadcrumb End -->

    @php
        $bgClass = match ($history->method) {
            'create' => 'bg-success text-light',
            'update' => 'bg-warning text-dark',
            'update status' => 'bg-primary text-light',
            'update status, update' => 'bg-dark text-light',
            'delete' => 'bg-danger text-light',
            default => 'bg-secondary text-dark',
        };
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h4 class="py-0 my-0">{{ $history->title }}</h4>
                <h4 class="py-0 my-0">{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i') }} WIB</h4>
            </div>
            <hr>
            <table class="mb-4">
                <tr>
                    <td class="pe-4">Nama</td>
                    <td>: {{ $history->name }}</td>
                </tr>
                <tr>
                    <td class="pe-4">Type</td>
                    <td>:
                        @if ($history->type === 'letter_out')
                            Surat Keluar
                        @elseif ($history->type === 'letter_in')
                            Surat Masuk
                        @else
                            {{ $history->type }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="pe-4">Method</td>
                    <td>: <span class="badge {{ $bgClass }}">{{ $history->method }}</span></td>
                </tr>
            </table>
            <div class="detail">
                {!! $history->detail !!}
            </div>
        </div>
    </div>
@endsection
