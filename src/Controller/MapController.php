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
    private function addSelectionForm($token, $categorie, $id)
    {
        $web = '<form data-turbo-frame="local_data" method="post">';
        $web .= '<input type="hidden" name="_token" value="' . $token . '">';
        $web .= '<input type="hidden" name="category" value="' . $categorie . '">';
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

            if (!empty($idElement)) {
                return $this->render('map/_map_details.html.twig', [
                    'items' => "Clicked on item " . $category . ' ' . $idElement,
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


        // Ajout d'icones specifiques
        /*
        $icon = Icon::url ('/icons/bars.png');
        $iconBars = $map.Icon({ options: '/icons/bars.png'  });
        $iconBars = new Icon()->url('/icons/beer.png');
        $iconBars = Icon::url('/icons/beer.png');
            ->size(40, 40)
            ->anchor(20, 40);
        */

        $bars = $overpass->getInArea('Rouen', 'bars');
        foreach ($bars as $key => $bar) {
            $marker = new Marker(
                position: new Point($bar['lat'], $bar['lon']),
                title: $bar['name'],
                infoWindow: new InfoWindow(
                    content: '<h3>Bar</h3><p>' . $bar['name'] . '<br>' . $bar['address'] . '</p>' . $this->addSelectionForm($token, "Bars", $key),
                )
            );

            $map->addMarker($marker);
        }

        $hotels = $overpass->getInArea('Rouen', 'hotels');
        foreach ($hotels as $key => $hotel) {
            $marker = new Marker(
                position: new Point($hotel['lat'], $hotel['lon']),
                title: $hotel['name'],
                infoWindow: new InfoWindow(
                    content: '<h3>Hotel</h3><p>' . $hotel['name'] . '<br>' . $hotel['address'] . '</p>' . $this->addSelectionForm($token, "Hotels", $key),
                )
            );

            $map->addMarker($marker);
        }

        $restaurants = $overpass->getInArea('Rouen', 'restaurants');
        foreach ($restaurants as $key => $restaurant) {
            $marker = new Marker(
                position: new Point($restaurant['lat'], $restaurant['lon']),
                title: $restaurant['name'],
                infoWindow: new InfoWindow(
                    content: '<h3>Restaurant</h3><p>' . $restaurant['name'] . '<br>' . $restaurant['address'] . '</p>' . $this->addSelectionForm($token, "Restaurant", $key),
                )
            );

            $map->addMarker($marker);
        }

        return $this->render('map/index.html.twig', [
            'map' => $map
        ]);
    }
}
