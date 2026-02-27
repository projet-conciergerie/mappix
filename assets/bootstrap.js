import { Application } from '@hotwired/stimulus'

// Without the Symfony-provided virtual module (removed in Symfony 8),
// register each controller explicitly. This avoids any reliance on
// Vite-specific helpers that might not be available in your build.
export const app = Application.start()

// import each controller by hand and register it by name
import MapController from './controllers/map_controller'
import GeolocateController from './controllers/geolocate_controller'
import HelloController from './controllers/hello_controller'
// csrf_protection_controller doesn't export a class; it's a vanilla module
// providing helper functions, so we don't register it with Stimulus.

app.register('map', MapController)
app.register('geolocate', GeolocateController)
app.register('hello', HelloController)


