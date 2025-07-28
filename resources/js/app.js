import './bootstrap';
import 'bootstrap';

import Alpine from 'alpinejs';
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.css';

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';


window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        closeButton: true
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([40.4168, -3.7038], 13); // Coordenadas de ejemplo (Madrid)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([40.4168, -3.7038]).addTo(map) // Marcador en la ubicación de ejemplo
        .bindPopup('Aquí está la propiedad')
        .openPopup();
});
