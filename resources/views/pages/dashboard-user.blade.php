@extends('layouts.main')

@section('content')
    <h1>Hi, {{ Auth::user()->name }}👋</h1>
    <div class="scan" style="height: calc(100svh - 300px);">
        <button class="btn btn-primary py-2 px-4 position-absolute top-50 start-50 translate-middle">
            <a href="{{ route('scan.index') }}" class="text-light text-decoration-none fw-semibold">Scan Me!</a>
        </button>
    </div>
@endsection
