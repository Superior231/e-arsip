<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ url('/assets/img/logo_ppj.png') }}" type="image/x-icon">
    @include('components.style', ['title' => 'Error 404: Page Not Found'])
    <style>
        .error-image {
            width: 400px;
        }
        .back:hover span {
            text-decoration: underline !important;
        }
        @media (max-width: 460px) {
            .error-img {
                width: 300px;
            }
        }
    </style>
</head>
<body style="background-color: #f1f5f8 !important;">
    <nav class="container-fluid py-3 px-2 px-md-5">
        <a href="{{ route('index') }}" class="back d-flex align-items-center text-decoration-none text-dark fw-bold gap-1">
            <i class="bx bx-chevron-left fs-3 fw-bold"></i>
            <span>Back to Home</span>
        </a>
    </nav>
    <div class="position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center justify-content-center gap-1">
        <img src="{{ url('assets/img/404.gif') }}" class="error-img" alt="Error gif">
        <h3 class="fw-bold">Page Not Found</h3>
    </div>
    @include('components.script')
</body>
</html>
