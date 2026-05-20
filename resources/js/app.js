import './bootstrap';
import '../sass/app.scss';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Ako učitavaš noUiSlider preko npm-a, otkomentariši liniju ispod:
// import noUiSlider from 'nouislider'; 

window.scrollToTop = function () {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('price-slider');
    const minInput = document.getElementById('min_price');
    const maxInput = document.getElementById('max_price');
    const rangeLabel = document.getElementById('price-range-label'); // Može da bude null, neće pući kod

    // Izbacio sam rangeLabel iz uslova - slider će raditi i bez njega!
    if (slider && minInput && maxInput) {
        
        // Bezbedna provera za biblioteku (bilo da je sa CDN-a ili npm-a)
        const sliderLib = typeof noUiSlider !== 'undefined' ? noUiSlider : (window.noUiSlider || null);
        
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
            range: {
                min: 0,
                max: 5000
            },
            format: {
                to: value => Math.round(value),
                from: value => parseFloat(value)
            }
        });

        slider.noUiSlider.on('update', function (values) {
            const min = values[0];
            const max = values[1];

            minInput.value = min;
            maxInput.value = max;

            // Provera: Ažuriraj label samo ako stvarno postoji u HTML-u
            if (rangeLabel) {
                rangeLabel.innerHTML =
                    `${parseInt(min).toLocaleString('de-DE')} € - ${parseInt(max).toLocaleString('de-DE')} €`;
            }
        });

        slider.noUiSlider.on('change', function () {
            const form = minInput.closest('form');
            if (form) form.submit();
        });
    }
});

import { initEditor } from './ckeditor';

document.addEventListener('DOMContentLoaded', function () {
    initEditor('#editor');
});