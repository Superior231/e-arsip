@extends('layouts.main')

@section('content')
    <div class="pengaturan-container d-flex flex-column gap-2 py-3">
        <a href="{{ route('profile.index') }}" class="text-decoration-none card border-0 py-4">
            <div class="row d-flex justify-content-between card-body py-0 px-4">
                <div class="col col-11 d-flex align-items-center gap-3">
                    <i class='bx bx-user fs-2'></i>
                    <div class="d-flex flex-column justify-content-center gap-1">
                        <h4 class="py-0 my-0">Profile</h4>
                        <span class="text-secondary py-0 my-0 fs-7">Edit profile, ubah password dan logout</span>
                    </div>
                </div>
                <div class="col col-1 d-flex justify-content-end align-items-center">
                    <i class='bx bx-chevron-right fs-2'></i>
                </div>
            </div>
        </a>
    </div>
@endsection
