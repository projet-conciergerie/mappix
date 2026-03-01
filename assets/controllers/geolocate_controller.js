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

        // helper that sets/updates a marker and recenters once
        const updateMarker = (coords) => {
            const lat = coords.latitude;
            const lng = coords.longitude;

            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
            } else {
                const icon = L.icon({
                    iconUrl: '/icons/marker_ici.png',
                    iconSize: [48, 48]
                })

                this.marker = L.marker([lat, lng], { icon, pane: 'markerPane' }).addTo(map);
            }

            // center the map on the first location fix only
            if (!this.hasCentered) {
                map.setView([lat, lng], 13);
                this.hasCentered = true;
            }
        };

        const geoOptions = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(
            (position) => updateMarker(position.coords),
            (error) => console.warn('GPS error', error),
            geoOptions
        );

        // watchPosition returns an id that we clear on disconnect
        this.watchId = navigator.geolocation.watchPosition(
            (position) => updateMarker(position.coords),
            (error) => console.warn('GPS watch error', error),
            geoOptions
        );
    }

    disconnect() {
        if (this.watchId != null && navigator.geolocation.clearWatch) {
            navigator.geolocation.clearWatch(this.watchId);
        }
    }
}
