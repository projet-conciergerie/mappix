import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        /*
         * Symfony UX Map dispatches an event when the map is ready.
         * The event name is: ux:map:connect
         */

        console.log('Geolocate controller connected');

        this.element.addEventListener('ux:map:connect', (event) => {
            const map = event.detail.map;
            
            if (!navigator.geolocation) {
                console.warn('Geolocation not supported');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Center map
                    map.setView([lat, lng], 13);
                },
                (error) => {
                    console.warn('GPS error', error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 5000
                }
            );
        });
    }
}
