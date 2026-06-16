
<section class="pc-hero-section">
    <div class="container py-5">
        <div class="pc-hero-card mx-auto">

            <div class="pc-hero-video-wrap" id="pcHeroWrap">

                <video
                    class="pc-hero-video"
                    id="pcHeroVideo"
                    muted
                    playsinline
                    preload="auto"
                    aria-hidden="true">
                    <source src="{{ asset('videos/0001-0060.webm') }}" type="video/webm">
                    <source src="{{ asset('videos/pc-hero.mp4') }}"  type="video/mp4">
                </video>

                <div class="pc-hero-overlay"></div>

                <div class="pc-hero-play-hint" id="pcPlayHint" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <polygon points="5,2 16,9 5,16" fill="currentColor"/>
                    </svg>
                </div>

                <div class="pc-hero-content">
                    <p class="pc-hero-eyebrow">PC Builder</p>
                    <h2 class="pc-hero-title">Složi svoj dream build</h2>
                    <p class="pc-hero-desc">
                        Treba ti pomoć da sastavis računar?Uz naš PC builder biranje komponenti je lako.
                    </p>
                    <a href="{{Route('builder.index')}}"
                       class="btn btn-primary fw-bold px-4 py-2 rounded-0 pc-hero-cta">
                        Počni da sastavljaš
                        <svg class="ms-2" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M2.5 7h9M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
.pc-hero-section {
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.pc-hero-card {
    max-width: 860px;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,0,0,0.6);
    border-radius: 0;
}

.pc-hero-video-wrap {
    position: relative;
    aspect-ratio: 16 / 7;
    background: #0a0a0a;
    overflow: hidden;
    cursor: pointer;
}

.pc-hero-video {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}

.pc-hero-overlay {
    position: absolute; inset: 0; pointer-events: none;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.0)  0%,
        rgba(0,0,0,0.1)  40%,
        rgba(0,0,0,0.72) 78%,
        rgba(0,0,0,0.90) 100%
    );
}

.pc-hero-play-hint {
    position: absolute;
    top: 14px; right: 16px;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.7);
    pointer-events: none;
    transition: opacity 0.25s, transform 0.25s;
    z-index: 3;
}

.pc-hero-play-hint {
    animation: pcHintPulse 2.5s ease-in-out infinite;
}
@keyframes pcHintPulse {
    0%, 100% { opacity: 0.7; transform: scale(1); }
    50%       { opacity: 1;   transform: scale(1.1); }
}

.pc-hero-video-wrap.is-playing .pc-hero-play-hint {
    opacity: 0;
    transform: scale(0.85);
    animation: none;
}

.pc-hero-video-wrap:hover .pc-hero-overlay {
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.0)  0%,
        rgba(0,0,0,0.05) 40%,
        rgba(0,0,0,0.65) 78%,
        rgba(0,0,0,0.88) 100%
    );
}

.pc-hero-content {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 2rem 2.5rem 2rem;
    z-index: 2;
    pointer-events: none;   
}

.pc-hero-cta {
    pointer-events: all;
}

.pc-hero-eyebrow {
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.2em;
    color: var(--bs-primary);
    margin: 0 0 0.3rem;
}

.pc-hero-title {
    font-size: clamp(1.4rem, 3.5vw, 2rem);
    font-weight: 800; letter-spacing: -0.03em;
    color: #fff; margin: 0 0 0.45rem;
    line-height: 1.15;
}

.pc-hero-desc {
    font-size: 0.88rem; color: rgba(255,255,255,0.58);
    margin: 0 0 1.2rem; max-width: 400px; line-height: 1.55;
}

.pc-hero-cta {
    display: inline-flex; align-items: center;
    font-size: 0.83rem; letter-spacing: 0.04em;
    text-transform: uppercase;
    transition: opacity 0.2s, transform 0.2s;
}
.pc-hero-cta:hover { opacity: 0.88; transform: translateX(2px); }
.pc-hero-cta svg  { transition: transform 0.2s; }
.pc-hero-cta:hover svg { transform: translateX(3px); }

@media (max-width: 576px) {
    .pc-hero-video-wrap { aspect-ratio: 4/3; }
    .pc-hero-content    { padding: 1.25rem; }
    .pc-hero-desc       { display: none; }
}

@media (prefers-reduced-motion: reduce) {
    .pc-hero-play-hint  { animation: none !important; }
    .pc-hero-cta,
    .pc-hero-cta svg    { transition: none; }
}
</style>

<script>
(function () {
    var wrap   = document.getElementById('pcHeroWrap');
    var video  = document.getElementById('pcHeroVideo');
    var hint   = document.getElementById('pcPlayHint');
    if (!wrap || !video) return;

    var isPlaying = false;
    var leaveWhilePlaying = false;

    function goToLastFrame() {
        if (video.duration && isFinite(video.duration)) {
            video.currentTime = video.duration - 0.001;
        }
    }

    video.addEventListener('loadedmetadata', function () {
        goToLastFrame();
    });

    if (video.readyState >= 1) {
        goToLastFrame();
    }

    wrap.addEventListener('mouseenter', function () {
        leaveWhilePlaying = false;
        if (isPlaying) return;   /* već se pušta, ne prekidaj */

        video.currentTime = 0;
        var playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise.then(function () {
                isPlaying = true;
                wrap.classList.add('is-playing');
            }).catch(function () {
            });
        }
    });

    wrap.addEventListener('mouseleave', function () {
        if (isPlaying) {
            leaveWhilePlaying = true;   
        }
    });

    video.addEventListener('ended', function () {
        isPlaying = false;
        leaveWhilePlaying = false;
        wrap.classList.remove('is-playing');
        goToLastFrame();
        video.pause();
    });

    video.addEventListener('error', function () {
        wrap.style.background = '#0d0d14';
        if (hint) hint.style.display = 'none';
    });

})();
</script>