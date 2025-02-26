@extends('layouts.main')

@push('styles')
    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 history-container">
        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bx-history fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">Total History</h4>
                            <span class="py-0 my-0">{{ $histories->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-purchase-tag fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">History Kategori</h4>
                            <span class="py-0 my-0">{{ $history_category->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-group fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">History Staff</h4>
                            <span class="py-0 my-0">23</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-share bx-flip-horizontal fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">History Mutasi</h4>
                            <span class="py-0 my-0">{{ $history_mutate->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-archive fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">History Arsip</h4>
                            <span class="py-0 my-0">12</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-decoration-none h-100">
                <div class="card-body">
                    <div class="card-info d-flex align-items-center gap-2">
                        <div class="icon">
                            <i class='bx bxs-envelope fs-3'></i>
                        </div>
                        <div class="detail">
                            <h4 class="py-0 my-0">History Surat</h4>
                            <span class="py-0 my-0">123</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="history mt-4">
        <h3 class="fw-semibold">Semua History</h3>
        <div class="table-responsive card p-4">
            <table id="historyTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center">Method</th>
                        <th>Type</th>
                        <th>Nama</th>
                        <th style="min-width: 250px;">Deskripsi</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $history)
                        @php
                            $bgClass = match ($history->method) {
                                'create' => 'bg-success text-light',
                                'update' => 'bg-warning text-dark',
                                'mutate' => 'bg-primary text-light',
                                'mutate, update' => 'bg-dark text-light',
                                'mutate, delete' => 'bg-danger text-light',
                                'mutate, in' => 'bg-primary text-light',
                                'mutate, out' => 'bg-danger text-light',
                                'delete' => 'bg-danger text-light',
                                default => 'bg-secondary text-dark',
                            };
                        @endphp

                        <tr>
                            <td class="text-center pe-4">
                                <span class="badge {{ $bgClass }}">{{ $history->method }}</span>
                            </td>
                            <td>{{ $history->type }}</td>
                            <td>{{ $history->name }}</td>
                            <td class="text-break">{{ $history->description }}</td>
                            <td class="text-nowrap">
                                <span class="d-none">{{ $history->created_at->format('Y m d H:i') }}</span>
                                {{ $history->created_at->format('d M Y H:i') }} WIB
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mutate mt-4">
        <h3 class="fw-semibold">Mutasi</h3>
        <div class="table-responsive card p-4">
            <table id="mutateTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center">Method</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th style="min-width: 250px;">Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($history_mutate as $item)
                        @php
                            $bgClass = match ($item->method) {
                                'mutate' => 'bg-primary text-light',
                                'mutate, update' => 'bg-dark text-light',
                                'mutate, delete' => 'bg-danger text-light',
                                'mutate, in' => 'bg-primary text-light',
                                'mutate, out' => 'bg-danger text-light',
                                default => 'bg-secondary text-dark',
                            };
                        @endphp

                        <tr>
                            <td class="text-center pe-4">
                                <span class="badge {{ $bgClass }}">{{ $item->method }}</span>
                            </td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->name }}</td>
                            <td class="text-break">{{ $item->description }}</td>
                            <td class="text-nowrap">
                                <span class="d-none">{{ $item->created_at->format('Y m d H:i') }}</span>
                                {{ $item->created_at->format('d M Y H:i') }} WIB
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
            $('#historyTable').DataTable({
                "language": {
                    "searchPlaceholder": "Search..."
                },
                "order": [[4, "desc"]]
            });
            $('#mutateTable').DataTable({
                "language": {
                    "searchPlaceholder": "Search..."
                },
                "order": [[4, "desc"]]
            });
        });
    </script>
@endpush
