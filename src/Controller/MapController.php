<?php

namespace App\Controller;

use App\Service\Overpass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\Icon\Icon;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

final class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(Request $request, Overpass $overpass): Response
    {

        if ($request->isMethod('POST')) {
            $idElement = $request->request->get('idElement');
            if (!empty($idElement)) {
                return $this->render('map/_map_details.html.twig', [
                    'items' => "Clicked on item " . $idElement,
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

        /*
        $map->addMarker(new Marker(
            position: new Point(49.433331, 1.08333),
            title: 'Ceppic',
            infoWindow: new InfoWindow(
                content: '<p>Welcome to Rouen <form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Rouen"><input type="submit" value="Infos"></form></p>',
            )
        ));
        */

        /*
        $icon = Icon::url ('/icons/bars.png');
        */
        /*
        $iconBars = $map.Icon({ options: '/icons/bars.png'  });
*/
        /*
        $iconBars = new Icon()->url('/icons/beer.png');

        $iconBars = Icon::url('/icons/beer.png');
*/
        /*
            ->size(40, 40)
            ->anchor(20, 40);
*/
/*
        $bars = $overpass->getInArea('Rouen', 'bars');
        foreach ($bars as $bar) {
            $marker = new Marker(
                position: new Point($bar['lat'], $bar['lon']),
                title: $bar['name'],
                infoWindow: new InfoWindow(
                    content: '<h3>Bar</h3><p>' . $bar['name'] . '<br>' . $bar['address'] . '<form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Rouen"><input type="submit" value="Infos"></form></p>',
                )
            );

            // $marker->icon();

            $map->addMarker($marker);
        }

        $hotels = $overpass->getInArea('Rouen', 'hotels');
        foreach ($hotels as $hotel) {
            $map->addMarker(new Marker(
                position: new Point($hotel['lat'], $hotel['lon']),
                title: $hotel['name'],
                infoWindow: new InfoWindow(
                    content: '<h3>Hotel</h3><p>' . $hotel['name'] . '<br>' . $hotel['address'] . '<form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Rouen"><input type="submit" value="Infos"></form></p>',
                )
            ));
        }

        $restaurants = $overpass->getInArea('Rouen', 'restaurants');
        foreach ($restaurants as $restaurant) {
            $map->addMarker(new Marker(
                position: new Point($restaurant['lat'], $restaurant['lon']),
                title: $restaurant['name'],
                infoWindow: new InfoWindow(
                    content: '<h3>Restaurant</h3><p>' . $restaurant['name'] . '<br>' . $restaurant['address'] . '<form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Rouen"><input type="submit" value="Infos"></form></p>',
                )
            ));
        }
        */

        $markers = [];

        $restaurants = $overpass->getInArea('Rouen', 'restaurants');
        foreach ($restaurants as $restaurant) {
            /*
            $map->addMarker(new Marker(
                position: new Point($restaurant['lat'], $restaurant['lon']),
                title: $restaurant['name'],
                infoWindow: new InfoWindow(
                    content: '<h3>Restaurant</h3><p>' . $restaurant['name'] . '<br>' . $restaurant['address'] . '<form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Rouen"><input type="submit" value="Infos"></form></p>',
                )
            ));
            */

            $markers[] = [
                'lat' => $restaurant['lat'],
                'lng' => $restaurant['lon'],
                'popup' => '<h3>Restaurant</h3><p>' . $restaurant['name'] . '<br>' . $restaurant['address'] . '<form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Rouen"><input type="submit" value="Infos"></form></p>'
            ];
        }

        return $this->render('map/index.html.twig', [
            'map' => $map,
            'markers' => $markers
        ]);
    }
}
