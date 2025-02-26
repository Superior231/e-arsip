<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ url('/assets/img/logo_ppj.png') }}" type="image/x-icon">
    @include('components.style')
</head>

<body>
    @include('components.sidebar')
    @include('components.navbar')
    @include('components.toast')
    <div class="dashboard px-3 px-lg-3 pb-5">
        @yield('content')
        @include('components.footer')
    </div>
    @include('components.script')
</body>

</html>
