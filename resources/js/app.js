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
    GLightbox({
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

    const latitude = Number.parseFloat(mapElement.dataset.latitude || '');
    const longitude = Number.parseFloat(mapElement.dataset.longitude || '');
    const address = (mapElement.dataset.address || '').trim();
    const zoom = Number.parseInt(mapElement.dataset.zoom || '14', 10);
    const mapTitle = mapElement.dataset.title || 'Ubicacion';
    const map = L.map(mapElement);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const renderMarker = function(targetLatitude, targetLongitude) {
        map.setView([targetLatitude, targetLongitude], zoom);

        L.marker([targetLatitude, targetLongitude]).addTo(map)
            .bindPopup(mapTitle)
            .openPopup();
    };

    if (!Number.isNaN(latitude) && !Number.isNaN(longitude)) {
        renderMarker(latitude, longitude);
        return;
    }

    if (address === '') {
        return;
    }

    const geocodeUrl = new URL('https://nominatim.openstreetmap.org/search');
    geocodeUrl.searchParams.set('format', 'json');
    geocodeUrl.searchParams.set('limit', '1');
    geocodeUrl.searchParams.set('q', address);

    fetch(geocodeUrl.toString(), {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('No se pudo geocodificar la direccion.');
            }

            return response.json();
        })
        .then(function(results) {
            const firstResult = Array.isArray(results) ? results[0] : null;

            if (!firstResult) {
                return;
            }

            const resolvedLatitude = Number.parseFloat(firstResult.lat || '');
            const resolvedLongitude = Number.parseFloat(firstResult.lon || '');

            if (Number.isNaN(resolvedLatitude) || Number.isNaN(resolvedLongitude)) {
                return;
            }

            renderMarker(resolvedLatitude, resolvedLongitude);
        })
        .catch(function() {
            map.remove();
        });
});
