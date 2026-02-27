import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        
        // assume map controller is registered on the same element
        const mapCtrl = this.application.getControllerForElementAndIdentifier(
            this.element,
            'map'
        );

        if (!mapCtrl) {
            console.warn('Map controller not found on element');
            return;
        }

        const map = mapCtrl.map;
        if (!map) {
            console.warn('Map instance not ready');
            return;
        }

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
    }
}
