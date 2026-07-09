<!-- Global Preloader Overlay -->
<div id="preloader" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #090d16;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.4s ease, visibility 0.4s ease;
">
    <div style="text-align: center; position: relative;">
        <!-- Pulsing Logo -->
        <div class="preloader-logo" style="
            width: 75px;
            height: 75px;
            background: rgba(255, 215, 0, 0.08);
            border: 1px solid rgba(255, 215, 0, 0.25);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #FFD700;
            box-shadow: 0 0 25px rgba(255, 215, 0, 0.12);
            margin: 0 auto;
            animation: pulse-preloader-logo 2s infinite ease-in-out;
        ">
            <i class="bi bi-globe-europe-africa" style="line-height: 1;"></i>
        </div>
        <!-- Spinner -->
        <div style="
            width: 45px;
            height: 45px;
            border: 3.5px solid rgba(255, 255, 255, 0.05);
            border-top: 3.5px solid #FFD700;
            border-right: 3.5px solid #3b82f6;
            border-radius: 50%;
            margin: 20px auto 0;
            animation: preloader-spin 0.8s linear infinite;
        "></div>
        <div style="
            color: #94a3b8;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 15px;
            letter-spacing: 0.5px;
        ">Loading EduLink...</div>
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
        box-shadow: 0 0 25px rgba(255, 215, 0, 0.12);
    }
    50% {
        transform: scale(1.08);
        box-shadow: 0 0 40px rgba(255, 215, 0, 0.25);
    }
}
.preloader-fade-out {
    opacity: 0 !important;
    visibility: hidden !important;
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
            }, 400);
        };
        
        // Fade out on window load
        if (document.readyState === 'complete') {
            fadeOut();
        } else {
            window.addEventListener('load', fadeOut);
        }
        
        // Safety fallback after 3.5 seconds
        setTimeout(fadeOut, 3500);
    }
})();
</script>
