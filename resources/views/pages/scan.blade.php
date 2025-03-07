@extends('layouts.main')

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center gap-2">
        <div id="reader" style="width: 300px;"></div>
        <p id="result" style="margin-top: 10px; font-weight: bold;"></p>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Validasi jika decodedText sudah berupa URL yang benar
            if (!decodedText.startsWith("http://") && !decodedText.startsWith("https://")) {
                decodedText = "https://" + decodedText;
            }
            console.log("Redirecting to:", decodedText);
            window.location.href = decodedText;
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            },
            /* verbose= */
            false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
@endpush
