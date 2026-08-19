<!-- Global Preloader Overlay -->
<div id="preloader" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.35s ease, visibility 0.35s ease;
">
    <div style="text-align: center; position: relative;">
        <!-- Pulsing Logo -->
        <div class="preloader-logo" style="
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #003366 0%, #002244 100%);
            border: 2px solid rgba(255, 215, 0, 0.4);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #FFD700;
            box-shadow: 0 10px 25px rgba(0, 51, 102, 0.25);
            margin: 0 auto;
            animation: pulse-preloader-logo 1.8s infinite ease-in-out;
        ">
            <i class="bi bi-mortarboard-fill" style="line-height: 1;"></i>
        </div>
        <!-- Spinner -->
        <div class="preloader-spinner" style="
            width: 40px;
            height: 40px;
            border: 3.5px solid #e2e8f0;
            border-top: 3.5px solid #003366;
            border-right: 3.5px solid #f59e0b;
            border-radius: 50%;
            margin: 18px auto 0;
            animation: preloader-spin 0.75s linear infinite;
        "></div>
        <div class="preloader-caption" style="
            color: #475569;
            font-family: 'Outfit', sans-serif;
            font-size: 0.92rem;
            font-weight: 600;
            margin-top: 14px;
            letter-spacing: 0.3px;
        ">EduLink Ghana ERP</div>
    </div>
</div>

<style>
@keyframes preloader-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
@keyframes pulse-preloader-logo {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 10px 25px rgba(0, 51, 102, 0.2);
    }
    50% {
        transform: scale(1.06);
        box-shadow: 0 14px 35px rgba(0, 51, 102, 0.35);
    }
}
.preloader-fade-out {
    opacity: 0 !important;
    visibility: hidden !important;
}

[data-bs-theme="dark"] #preloader {
    background: #090f1d !important;
}
[data-bs-theme="dark"] .preloader-spinner {
    border-color: rgba(255, 255, 255, 0.1) !important;
    border-top-color: #58a6ff !important;
    border-right-color: #FFD700 !important;
}
[data-bs-theme="dark"] .preloader-caption {
    color: #cbd5e1 !important;
}
</style>

<script>
(function() {
    var preloader = document.getElementById('preloader');
    if (preloader) {
        var fadeOut = function() {
            preloader.classList.add('preloader-fade-out');
            setTimeout(function() {
                if (preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
            }, 350);
        };
        
        // Fade out on window load
        if (document.readyState === 'complete') {
            fadeOut();
        } else {
            window.addEventListener('load', fadeOut);
        }
        
        // Safety fallback after 2.5 seconds
        setTimeout(fadeOut, 2500);
    }
})();
</script>
