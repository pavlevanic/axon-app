import './bootstrap';
import '../sass/app.scss';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

window.scrollToTop = function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('price-slider');
    const minInput = document.getElementById('min_price');
    const maxInput = document.getElementById('max_price');
    const rangeLabel = document.getElementById('price-range-label');

    // Početne vrednosti: Proveri request, ako nema koristi 0 i 5000
    let startMin = parseInt(minInput.value) || 0;
    let startMax = parseInt(maxInput.value) || 5000; 

    if (slider) {
        noUiSlider.create(slider, {
            start: [startMin, startMax],
            connect: true,
            step: 10, // Pomeraj od po 10 evra
            range: {
                'min': 0,
                'max': 5000 // Podesi maksimalnu cenu u evrima koju imaš u prodavnici
            },
            format: {
                to: value => Math.round(value),
                from: value => parseFloat(value)
            }
        });

        slider.noUiSlider.on('update', function (values, handle) {
            const min = values[0];
            const max = values[1];
            
            minInput.value = min;
            maxInput.value = max;
            
            // Formatiranje ispisa: 1.250 €
            rangeLabel.innerHTML = `${parseInt(min).toLocaleString('de-DE')} € - ${parseInt(max).toLocaleString('de-DE')} €`;
        });

        // Submit forme nakon što korisnik prestane da pomera klizač
        slider.noUiSlider.on('change', function () {
            minInput.closest('form').submit();
        });
    }
});



