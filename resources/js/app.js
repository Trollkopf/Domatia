import './bootstrap';
import 'bootstrap';
import './swiper';

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
    const favoriteForms = document.querySelectorAll('[data-favorite-toggle-form]');
    const favoriteCountBadge = document.querySelector('[data-favorites-count]');

    if (!favoriteForms.length) {
        return;
    }

    const syncFavoriteUI = function(propertySlug, isFavorite, favoritesCount) {
        document.querySelectorAll(`[data-favorite-toggle-form][data-property-slug="${propertySlug}"]`).forEach(function(form) {
            const button = form.querySelector('[data-favorite-toggle-button]');
            const icon = form.querySelector('[data-favorite-toggle-icon]');
            const label = form.querySelector('[data-favorite-toggle-label]');
            const isCardFavorite = form.classList.contains('property-teaser-favorite');

            if (button) {
                button.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
                button.classList.remove('btn-dark', 'btn-light', 'btn-outline-dark');

                if (isCardFavorite) {
                    button.classList.add(isFavorite ? 'btn-dark' : 'btn-light');
                } else {
                    button.classList.add(isFavorite ? 'btn-dark' : 'btn-outline-dark');
                }
            }

            if (icon) {
                icon.classList.toggle('fa-solid', isFavorite);
                icon.classList.toggle('fa-regular', !isFavorite);
            }

            if (label) {
                label.textContent = isFavorite ? 'Guardada' : 'Favorita';
            }
        });

        if (favoriteCountBadge) {
            favoriteCountBadge.textContent = String(favoritesCount);
            favoriteCountBadge.classList.toggle('d-none', favoritesCount === 0);
        }
    };

    favoriteForms.forEach(function(form) {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const submitButton = form.querySelector('[data-favorite-toggle-button]');

            if (submitButton?.disabled) {
                return;
            }

            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('No se pudo actualizar favoritos.');
                }

                const payload = await response.json();

                syncFavoriteUI(
                    payload.property_slug,
                    payload.is_favorite,
                    payload.favorites_count
                );
            } catch (error) {
                form.submit();
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const mapElement = document.getElementById('map');

    if (!mapElement) {
        return;
    }

    var map = L.map('map').setView([40.4168, -3.7038], 13); // Coordenadas de ejemplo (Madrid)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([40.4168, -3.7038]).addTo(map) // Marcador en la ubicación de ejemplo
        .bindPopup('Aquí está la propiedad')
        .openPopup();
});
