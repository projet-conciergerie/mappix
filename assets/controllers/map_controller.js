import { Controller } from '@hotwired/stimulus'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

import 'leaflet.markercluster'
import 'leaflet.markercluster/dist/MarkerCluster.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'

export default class extends Controller {
    static values = {
        categories: Object,
        token: String
    }

    connect() {
        this.map = L.map(this.element).setView([49.433331, 1.08333], 13)

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(this.map)

        this.clusterGroup = L.markerClusterGroup()
        this.map.addLayer(this.clusterGroup)

        this.loadMarkers()
    }

    loadMarkers() {
        for (const [category, items] of Object.entries(this.categoriesValue)) {
            items.forEach((item, index) => {
                const icon = L.icon({
                    iconUrl: `/icons/marker_${category.toLowerCase()}.png`,
                    iconSize: [64, 64]
                })

                const marker = L.marker([item.lat, item.lon], { icon })

                const popup = `
                    <h3>${category}</h3>
                    <p>${item.name}<br>${item.address}</p>
                    <form data-turbo-frame="local_data" method="post">
                        <input type="hidden" name="_token" value="${this.tokenValue}">
                        <input type="hidden" name="category" value="${category}">
                        <input type="hidden" name="idElement" value="${index}">
                        <input type="submit" value="Infos">
                    </form>
                `

                marker.bindPopup(popup)

                this.clusterGroup.addLayer(marker)
            })
        }
    }
}
