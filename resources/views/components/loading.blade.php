<div id="loading-screen" class="bg-color" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%; height: 100%; z-index: 9999;">
    <div class="container d-flex flex-column align-items-center justify-content-center h-100 w-100">
        <img src="{{ url('assets/img/logo_ppj.png') }}" alt="Logo" style="width: 120px; height: auto; border-radius: 10%;">
        <h1 class="fw-bold text-color text-center mt-3">Putra Panggil Jaya</h1>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loadingScreen = document.getElementById('loading-screen');
        if (loadingScreen) {
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 3000);
        }
    });
</script>