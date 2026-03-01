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
            
            items.datas.forEach((item, index) => {
                const icon = L.icon({
                    iconUrl: `/icons/${items.icon}`,
                    iconSize: [64, 64]
                })

                const marker = L.marker([item.lat, item.lon], { icon })

                const popup = `
                    <h3 class="text-2xl font-bold">${items.display}</h3>
                    <p class="text-xl font-bold">${item.name}</p>
                    <p class="text-lg">${item.address}</p>
                    <form data-turbo-frame="local_data" method="post">
                        <input type="hidden" name="_token" value="${this.tokenValue}">
                        <input type="hidden" name="category" value="${category}">
                        <input type="hidden" name="idElement" value="${index}">
                        <input class="cursor-pointer bg-sky-500 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded" type="submit" value="Infos">
                    </form>
                `

                marker.bindPopup(popup)

                this.clusterGroup.addLayer(marker)
            })
        }
    }
}
