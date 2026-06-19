import './bootstrap';
import '../sass/app.scss';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

window.scrollToTop = function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
document.addEventListener('DOMContentLoaded', function () {
    const backToTopBtn = document.getElementById('back-to-top-btn');

    if (backToTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 600) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const slider    = document.getElementById('price-slider');
    const minInput  = document.getElementById('min_price');
    const maxInput  = document.getElementById('max_price');
    const rangeLabel = document.getElementById('price-range-label');

    if (slider && minInput && maxInput) {
        const sliderLib = window.noUiSlider || null;

        if (!sliderLib) {
            console.error('AXON: noUiSlider biblioteka nije učitana!');
            return;
        }

        let startMin = parseInt(minInput.value) || 0;
        let startMax = parseInt(maxInput.value) || 5000;

        sliderLib.create(slider, {
            start: [startMin, startMax],
            connect: true,
            step: 10,
            range: { min: 0, max: 5000 },
            format: {
                to: value => Math.round(value),
                from: value => parseFloat(value)
            }
        });

        slider.noUiSlider.on('update', function (values) {
            minInput.value = values[0];
            maxInput.value = values[1];
            if (rangeLabel) {
                rangeLabel.innerHTML = `${parseInt(values[0]).toLocaleString('de-DE')} € - ${parseInt(values[1]).toLocaleString('de-DE')} €`;
            }
        });

        slider.noUiSlider.on('change', function () {
            const form = minInput.closest('form');
            if (form) form.submit();
        });
    }
});

/* ═══════════════════════════════════════════════════════════
   AXON GLOBAL PELL EDITOR INTEGRATION
   ═══════════════════════════════════════════════════════════ */
   document.addEventListener('DOMContentLoaded', function () {
    const editorDiv = document.getElementById('axon-pell-editor');
    const txtArea = document.getElementById('axon-pell-textarea');
    const sourceBtn = document.getElementById('axon-source-btn');
    
    if (!editorDiv || !txtArea || !sourceBtn) return;

    let isSourceMode = false;

    // Pronalazimo formu u kojoj se editor nalazi
    const parentForm = txtArea.closest('form');

    const script = document.createElement('script');
    script.src = "https://unpkg.com/pell/dist/pell.min.js";
    document.head.appendChild(script);

    script.onload = function () {
        const editor = window.pell.init({
            element: editorDiv,
            onChange: html => {
                if (!isSourceMode) {
                    txtArea.value = html;
                }
            },
            defaultParagraphSeparator: 'p',
            actions: [
                'bold', 'italic', 'underline', 'heading1', 'heading2', 'olist', 'ulist', 'link', 'image',
                {
                    name: 'video',
                    icon: '<b>V</b>',
                    title: 'Ubaci Video',
                    result: () => {
                        const url = prompt('Unesite putanju do videa (npr. /videos/fajl.webm):');
                        if (url) {
                            const videoHtml = `
                                <div class="mb-4 text-center">
                                    <video class="w-100 rounded shadow-sm" controls style="max-width: 100%; height: auto;">
                                        <source src="${url}" type="video/webm">
                                        <source src="${url.replace('.webm', '.mp4')}" type="video/mp4">
                                        Vaš browser ne podržava video.
                                    </video>
                                </div><p></p>`;
                            window.pell.exec('insertHTML', videoHtml);
                        }
                    }
                }
            ]
        });

        // FIX 1: Pouzdaniji način za inicijalno punjenje editora (i za Edit i za Create nakon greške)
        const pellContent = editorDiv.querySelector('.pell-content');
        if (txtArea.value && pellContent) {
            pellContent.innerHTML = txtArea.value;
        }

        // FIX 2: Osiguravamo da je textarea sakriven na početku, bez obzira na Laravel old() refreš
        txtArea.style.display = 'none';
        txtArea.classList.remove('source-editor-active');

        // Režim izmene izvornog koda (Source)
        sourceBtn.addEventListener('click', function () {
            if (!isSourceMode) {
                editorDiv.style.style.display = 'none';
                txtArea.style.display = 'block'; // Prikazujemo textarea
                txtArea.classList.add('source-editor-active');
                sourceBtn.textContent = 'Visual Editor';
                sourceBtn.classList.remove('btn-outline-dark');
                sourceBtn.classList.add('btn-dark');
                isSourceMode = true;
            } else {
                if (pellContent) {
                    pellContent.innerHTML = txtArea.value;
                }
                txtArea.style.display = 'none'; // Ponovo sklanjamo textarea
                txtArea.classList.remove('source-editor-active');
                editorDiv.style.display = 'block';
                sourceBtn.textContent = 'Source';
                sourceBtn.classList.remove('btn-dark');
                sourceBtn.classList.add('btn-outline-dark');
                isSourceMode = false;
            }
        });

        if (parentForm) {
            parentForm.addEventListener('submit', function () {
                if (isSourceMode) {
                    if (pellContent) pellContent.innerHTML = txtArea.value;
                } else {
                    if (pellContent) txtArea.value = pellContent.innerHTML;
                }
            });
        }
    };
});