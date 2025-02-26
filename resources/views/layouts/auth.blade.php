<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ url('/assets/img/logo_ppj.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ url('assets/vendors/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendors/boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ url('/assets/css/auth.css') }}">
    <title>{{ $title }}</title>
</head>

<body>
    @include('components.loading')
    <div class="d-flex justify-content-end">
        @include('components.toast')
    </div>
    <div class="px-0 mx-0 d-flex justify-content-center w-100">
        @yield('content')
    </div>
    <script src="{{ url('/assets/js/auth.js') }}"></script>
    @include('components.script')
</body>

</html>
