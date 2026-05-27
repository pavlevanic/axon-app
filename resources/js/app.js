import './bootstrap';
import '../sass/app.scss';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

window.scrollToTop = function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

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

document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('#editor')) {
        import('./ckeditor').then(({ initEditor }) => {
            initEditor('#editor');
        });
    }
});