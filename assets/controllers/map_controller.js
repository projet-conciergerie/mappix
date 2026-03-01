import { Controller } from '@hotwired/stimulus'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

import 'leaflet.markercluster'
import 'leaflet.markercluster/dist/MarkerCluster.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'

import 'leaflet.featuregroup.subgroup'

export default class extends Controller {
    static values = {
        categories: Object,
        token: String
    }

    layerMenuOpenned = false;

    groups = [];

    connect() {
        this.map = L.map(this.element).setView([49.433331, 1.08333], 13)

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(this.map)

        this.clusterGroup = L.markerClusterGroup()
        this.map.addLayer(this.clusterGroup)

        this.loadMarkers()

        // create layer toggles

        const mapLayers = document.querySelector('.map-layers')

        const groupdiv = document.createElement('div');
        groupdiv.classList.add('hidden', 'map-layer-group', 'flex', 'flex-col', 'gap-2');

        this.groups.forEach((group, index) => {

            const toggle = document.createElement('button');
            toggle.classList.add('map-layer-toggle', 'flex', 'items-center', 'gap-2', 'px-2', 'py-1', 'rounded', 'bg-white', 'shadow');
            toggle.dataset.index = index;

            const icon = document.createElement('img');
            icon.src = `/icons/${group.icon}`;
            icon.alt = group.name;
            icon.classList.add('map-layer-icon');

            const label = document.createElement('span');
            label.textContent = group.name;

            toggle.appendChild(icon);
            toggle.appendChild(label);
            groupdiv.appendChild(toggle);

            // add / remove layer to map for initial state

            if (group.active) {
                this.map.addLayer(group.layer);
                toggle.classList.add('map-layer-toggle-active');
            } else {
                this.map.removeLayer(group.layer);
                toggle.classList.remove('map-layer-toggle-active');
            }

        });

        mapLayers.appendChild(groupdiv);

        // open layer menu on first click on control button
        document.querySelector('.map-layers-icon').addEventListener('click', () => {
            this.layerMenuOpenned = !this.layerMenuOpenned;
            groupdiv.classList.toggle('hidden', !this.layerMenuOpenned);
        });

        mapLayers.addEventListener('click', (e) => {
            if (e.target.classList.contains('map-layer-toggle')) {
                const index = e.target.dataset.index;

                const layer = this.groups[index].layer;

                if (this.map.hasLayer(layer)) {
                    this.map.removeLayer(layer);
                    e.target.classList.remove('map-layer-toggle-active');
                } else {
                    this.map.addLayer(layer);
                    e.target.classList.add('map-layer-toggle-active');
                }
            }
        })
    }

    loadMarkers() {
        this.group = [];

        for (const [category, items] of Object.entries(this.categoriesValue)) {

            const icon = L.icon({
                iconUrl: `/icons/${items.icon}`,
                iconSize: [48, 48]
            })

            const subgroup = L.featureGroup.subGroup(this.clusterGroup)

            this.groups.push({
                name: items.display,
                layer: subgroup,
                icon: items.icon,
                active: true
            })

            items.datas.forEach((item, index) => {

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

                subgroup.addLayer(marker)
            })
        }
    }
}
