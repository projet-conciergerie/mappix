import './stimulus_bootstrap.js';

/*
 * Leaflet MarkerCluster CSS
 */

import './vendor/leaflet/dist/leaflet.min.css';             // already present
import './vendor/leaflet-markercluster/MarkerCluster.css';
import './vendor/leaflet-markercluster/MarkerCluster.Default.css';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
