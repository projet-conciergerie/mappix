import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {}
    static targets = []

    connect() {
        if (!navigator.geolocation) {
            console.warn('Geolocation not supported');
            return;
        }
    }

    disconnect() {
        if (this.watchId != null && navigator.geolocation.clearWatch) {
            navigator.geolocation.clearWatch(this.watchId);
        }
    }

    startGeolocate() {

        if (!navigator.geolocation) {
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

                this.marker = L.marker([lat, lng], { icon, pane: 'markerPane' }).addTo(this.map);
            }

            // if the position is fixed by the user search, we don't want to recenter on geolocation updates
            if (this.positionSet) {
                this.hasCentered = true;
            }

            // center the map on the first location fix only
            if (!this.hasCentered) {
                this.map.setView([lat, lng], 13);
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

        document.querySelector('.map-center-button').addEventListener('click', () => {
            if (this.marker) {
                this.map.setView(this.marker.getLatLng(), 13);
            }
        });
    }

    onMapLoaded(event) {
        this.map = event.detail.map;
        this.positionSet = event.detail.positionSet;

        this.startGeolocate();
    }
}
