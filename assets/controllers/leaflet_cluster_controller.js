import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static values = {
    markers: Array,
  };

  async connect() {
    // Ensure Leaflet is available, then load markercluster plugin
    await this._ensureMarkerCluster();
    
    // Wait for the map to be ready
    this._waitForMap();
  }

  async _ensureMarkerCluster() {
    // If markercluster already loaded, skip
    if (window.L?.markerClusterGroup) {
      return;
    }

    // Wait for Leaflet to be available
    if (!window.L) {
      await new Promise(resolve => setTimeout(resolve, 100));
      return this._ensureMarkerCluster();
    }

    // Load markercluster plugin dynamically
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = '/assets/vendor/leaflet-markercluster/leaflet.markercluster.js';
      script.onload = () => {
        console.log('MarkerCluster plugin loaded');
        resolve();
      };
      script.onerror = () => {
        console.error('Failed to load markercluster plugin');
        reject(new Error('MarkerCluster plugin failed to load'));
      };
      document.head.appendChild(script);
    });
  }

  _waitForMap() {
    const map = this._findMapInstance();
    
    if (!map) {
      setTimeout(() => this._waitForMap(), 100);
      return;
    }

    // Map is ready — initialize cluster
    this._initializeCluster(map);
  }

  _initializeCluster(map) {
    const cluster = L.markerClusterGroup();
    const markers = this.markersValue || [];

    markers.forEach(markerData => {
      const marker = L.marker([markerData.lat, markerData.lng]);
      
      if (markerData.popup) {
        marker.bindPopup(markerData.popup);
      }
      
      cluster.addLayer(marker);
    });

    map.addLayer(cluster);
    console.log(`Clustered ${markers.length} markers`);
  }

  _findMapInstance() {
    // Try common places where ux-leaflet stores the map
    const container = this.element;
    
    // Direct property on container
    if (container._leaflet_map) return container._leaflet_map;
    
    // Check nested elements (ux_map renders into a child)
    const leafletChildren = container.querySelectorAll('[class*="leaflet-"]');
    for (let child of leafletChildren) {
      if (child._leaflet_map) return child._leaflet_map;
    }

    // Last resort: global window reference
    return window.map || null;
  }
}