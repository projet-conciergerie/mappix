<?php

namespace App\Controller;

use App\Service\Overpass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\Icon\Icon;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

final class MapController extends AbstractController
{
    /**
     * addSelectionForm Create a frame for a marker popup on the map
     * @param mixed $token
     * @param mixed $categorie
     * @param mixed $id
     * @return string
     */
    private function addSelectionForm($token, $category, $id)
    {
        $web = '<form data-turbo-frame="local_data" method="post">';
        $web .= '<input type="hidden" name="_token" value="' . $token . '">';
        $web .= '<input type="hidden" name="category" value="' . $category . '">';
        $web .= '<input type="hidden" name="idElement" value="' . $id . '">';
        $web .= '<input type="submit" value="Infos">';
        $web .= '</form>';

        return $web;
    }

    #[Route('/map', name: 'app_map')]
    public function index(Request $request, Overpass $overpass, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $csrfTokenManager->getToken('map_form')->getValue();

        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('_token');
            if (
                !$csrfTokenManager->isTokenValid(
                    new CsrfToken('map_form', $submittedToken)
                )
            ) {
                throw $this->createAccessDeniedException('Token CSRF invalide');
            }

            $idElement = $request->request->get('idElement');
            $category = $request->request->get('category');

            $datas = [];

            if ($category === "Bars") {
                $datas = $overpass->getInArea('Rouen', 'bars');
            } else if ($category === "Hotels") {
                $datas = $overpass->getInArea('Rouen', 'hotels');
            } else if ($category === 'Restaurants') {
                $datas = $overpass->getInArea('Rouen', 'restaurants');
            } else if ($category === 'Fontaines') {
                $datas = $overpass->getInArea('Rouen', 'fontaines');
            } else if ($category === 'Toilettes') {
                $datas = $overpass->getInArea('Rouen', 'toilettes');
            }

            if (!empty($idElement) && !empty($category)) {
                $data = $datas[$idElement];

                return $this->render('map/_map_details.html.twig', [
                    'category' => $category,
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'datas' => $data['tags']
                ]);
            }
        }

        $map = (new Map('default'))
            ->center(new Point(49.433331, 1.08333))
            ->zoom(8)
            ->options(
                (new LeafletOptions())
                    ->tileLayer(new TileLayer(
                        url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        options: ['maxZoom' => 19]
                    ))
            );

        $iconBars = Icon::url('icons/marker_bars.png')
            ->width(64)
            ->height(64);

        $bars = $overpass->getInArea('Rouen', 'bars');
        foreach ($bars as $key => $bar) {
            $marker = new Marker(
                position: new Point($bar['lat'], $bar['lon']),
                title: $bar['name'],
                icon: $iconBars,
                infoWindow: new InfoWindow(
                    content: '<h3>Bar</h3><p>' . $bar['name'] . '<br>' . $bar['address'] . '</p>' . $this->addSelectionForm($token, "Bars", $key),
                )
            );

            $map->addMarker($marker);
        }

        $iconHotels = Icon::url('icons/marker_hotels.png')
            ->width(64)
            ->height(64);

        $hotels = $overpass->getInArea('Rouen', 'hotels');
        foreach ($hotels as $key => $hotel) {
            $marker = new Marker(
                position: new Point($hotel['lat'], $hotel['lon']),
                title: $hotel['name'],
                icon: $iconHotels,
                infoWindow: new InfoWindow(
                    content: '<h3>Hotel</h3><p>' . $hotel['name'] . '<br>' . $hotel['address'] . '</p>' . $this->addSelectionForm($token, "Hotels", $key),
                )
            );

            $map->addMarker($marker);
        }

        $iconRestaurants = Icon::url('icons/marker_restaurants.png')
            ->width(64)
            ->height(64);

        $restaurants = $overpass->getInArea('Rouen', 'restaurants');
        foreach ($restaurants as $key => $restaurant) {
            $marker = new Marker(
                position: new Point($restaurant['lat'], $restaurant['lon']),
                title: $restaurant['name'],
                icon: $iconRestaurants,
                infoWindow: new InfoWindow(
                    content: '<h3>Restaurant</h3><p>' . $restaurant['name'] . '<br>' . $restaurant['address'] . '</p>' . $this->addSelectionForm($token, "Restaurants", $key),
                )
            );

            $map->addMarker($marker);
        }

        $iconFountains = Icon::url('icons/marker_fontains.png')
            ->width(64)
            ->height(64);

        $fontaines = $overpass->getInArea('Rouen', 'fontaines');
        foreach ($fontaines as $key => $fontaine) {
            $marker = new Marker(
                position: new Point($fontaine['lat'], $fontaine['lon']),
                title: $fontaine['name'],
                icon: $iconFountains,
                infoWindow: new InfoWindow(
                    content: '<h3>Fontaines</h3><p>' . $fontaine['name'] . '<br>' . $fontaine['address'] . '</p>' . $this->addSelectionForm($token, "Fontaines", $key),
                )
            );

            $map->addMarker($marker);
        }

        $iconToilets = Icon::url('icons/marker_toilets.png')
            ->width(64)
            ->height(64);

        $toilettes = $overpass->getInArea('Rouen', 'toilettes');
        foreach ($toilettes as $key => $toilette) {
            $marker = new Marker(
                position: new Point($toilette['lat'], $toilette['lon']),
                title: $toilette['name'],
                icon: $iconToilets,
                infoWindow: new InfoWindow(
                    content: '<h3>Toilettes</h3><p>' . $toilette['name'] . '<br>' . $toilette['address'] . '</p>' . $this->addSelectionForm($token, "Toilettes", $key),
                )
            );

            $map->addMarker($marker);
        }

        return $this->render('map/index.html.twig', [
            'map' => $map
        ]);
    }
}
