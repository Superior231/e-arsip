<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ url('/assets/img/logo_ppj.png') }}" type="image/x-icon">
    @include('components.style')
</head>

<body>
    @yield('content')
    @include('components.footer')
    @include('components.script')
</body>

</html>
