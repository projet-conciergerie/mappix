import { Controller } from '@hotwired/stimulus'
import L from 'leaflet'
import GestureHandling from 'leaflet-gesture-handling'

import 'leaflet/dist/leaflet.css'
import "leaflet-gesture-handling/dist/leaflet-gesture-handling.css";

import 'leaflet.markercluster'
import 'leaflet.markercluster/dist/MarkerCluster.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'

import 'leaflet.featuregroup.subgroup'

export default class extends Controller {
    static values = {
        categories: Object,
        position: Object,
        token: String
    }

    layerMenuOpenned = false;

    groups = [];

    connect() {
        // if position value is provided, we consider the position is fixed by the user search and not by geolocation
        this.positionSet = Object.keys(this.positionValue).length !== 0;
        
        this.map = L.map(this.element, {
            gestureHandling: true,
            gestureHandlingOptions: {
                text: {
                    touch: "Use two fingers to move the map",
                    scroll: "Use ctrl + scroll to zoom the map",
                    scrollMac: "Use ⌘ + scroll to zoom the map"
                }
            }
        }).setView([49.433331, 1.08333], 13)

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(this.map)

        this.clusterGroup = L.markerClusterGroup()
        this.map.addLayer(this.clusterGroup)

        this.loadMarkers()

        // Center map on searched object if position value is provided
        if (this.positionSet) {
            this.map.setView([this.positionValue.lat, this.positionValue.lon], 19)
        }

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

            const led = document.createElement('div');
            led.classList.add('map-layer-toggle-led');

            toggle.appendChild(icon);
            toggle.appendChild(label);
            toggle.appendChild(led);

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
        document.querySelector('.map-layers-button').addEventListener('click', () => {
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

        // close layers menu on map click
        this.map.on('click', () => {
            if (this.layerMenuOpenned) {
                this.layerMenuOpenned = false;
                groupdiv.classList.add('hidden');
            }
        });

        // dispatch event to let know the map is loaded and provide the map instance for other controllers
        // Let know if the position is fixed by the user search or if we should use geolocation

        queueMicrotask(() => {
            this.dispatch("loaded", {
                detail: {
                    map: this.map,
                    positionSet: this.positionSet
                }
            });
        });
    }

    disconnect() {
        this.map.remove();
    }

    loadMarkers() {
        this.group = [];

        for (const [category, items] of Object.entries(this.categoriesValue)) {

            const icon = L.icon({
                iconUrl: `/icons/${items.icon}`,
                iconSize: [48, 48]
            })

            const subgroup = L.featureGroup.subGroup(this.clusterGroup)

            let active = true;
            if (this.positionSet) {
                if (category !== this.positionValue.category) {
                    active = false;
                }
            }

            this.groups.push({
                name: items.display,
                layer: subgroup,
                icon: items.icon,
                active: active
            })

            items.datas.forEach((item, index) => {

                const marker = L.marker([item.lat, item.lon], { icon })

                const popup = `
                    <h3 class="text-2xl font-bold">${items.display}</h3>
                    <p class="text-xl font-bold">${item.name ? item.name : 'Pas de Nom disponible'}</p>
                    <p class="text-lg">${item.address}</p>
                    <form data-turbo="false">
                        <input type="hidden" name="_token" value="${this.tokenValue}">
                        <input type="hidden" name="category" value="${category}">
                        <input type="hidden" name="idElement" value="${index}">
                        <input class="cursor-pointer bg-sky-500 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded" type="submit" value="Infos">
                    </form>
                `

                marker.on('popupopen', (e) => {
                    const popupElement = e.popup.getElement();
                    const form = popupElement.querySelector('form');

                    form.addEventListener('submit', (e) => {
                        e.preventDefault();

                        const formData = new FormData(form);

                        fetch('/map/data', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.text())
                            .then(html => {
                                document.getElementById('map-details').innerHTML = html;
                            })
                    })
                })

                marker.bindPopup(popup)

                subgroup.addLayer(marker)
            })
        }
    }
}
