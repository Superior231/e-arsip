@extends('layouts.auth', ['title' => 'Login - Putra Panggil Jaya'])

@section('content')
    <div class="row w-100" style="height: 100svh;">
        <div class="col col-12 col-md-6 col-lg-7 d-flex flex-column justify-content-center" id="hero">
            <div class="logo d-flex align-items-center gap-2">
                <img src="{{ url('assets/img/logo_ppj.png') }}" alt="Logo"
                    style="width: 80px; height: auto;">
                <h4 class="text-color text-center">Putra Panggil Jaya</h4>
            </div>
            <div class="d-flex">
                <img src="{{ url('assets/img/archives.webp') }}" alt="Login"
                    style="width: 85%; height: auto;">
            </div>
        </div>
        <div class="col col-12 col-sm-12 col-md-6 col-lg-5 d-flex flex-column justify-content-center">
            <div class="d-flex flex-column justify-content-between h-100">
                <div class="container d-flex flex-column justify-content-center px-auto px-md-5 h-100">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-4 d-flex flex-column align-items-center d-none" id="logo-mobile">
                            <img src="{{ url('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 80px; height: auto;">
                            <h4>Putra Panggil Jaya</h4>
                        </div>
                        <h3 class="fw-bold">Sign in</h3>
                        <p>Masuk Aplikasi e-Arsip</p>
                    </div>
    
                    <form method="POST" action="{{ route('login') }}" class="auth mt-4">
                        @csrf
    
                        <div class="content mb-3">
                            <div class="pass-logo">
                                <i class='bx bx-user'></i>
                            </div>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Name"
                                value="{{ old('name') }}" required autocomplete="name" autofocus>
    
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
    
                        <div class="content mb-3">
                            <div class="pass-logo">
                                <i class='bx bx-lock-alt'></i>
                            </div>
                            <div class="d-flex align-items-center position-relative">
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" style="padding-right: 45px;"
                                    placeholder="Password" required>
                                <div class="showPass d-flex align-items-center justify-content-center position-absolute end-0 h-100"
                                    id="showPass" style="cursor: pointer; width: 50px; border-radius: 0px 10px 10px 0px;"
                                    onclick="showPass()">
                                    <i class="fa-regular fa-eye-slash"></i>
                                </div>
                            </div>
    
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button class="btn btn-primary d-block fw-semibold w-100 mt-4" type="submit">Login</button>
                    </form>
                </div>
                <div class="footer d-flex justify-content-center py-5" style="height: 20px">
                    <small class="text-secondary">Copyright &copy;2025, Putra Panggil Jaya All Rights Reserved.</small>
                </div>
            </div>
        </div>
    </div>
@endsection
