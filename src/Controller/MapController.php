<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

final class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(Request $request): Response
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

            ->addMarker(new Marker(
                position: new Point(49.490034, 1.140310),
                title: 'Ceppic',
                infoWindow: new InfoWindow(
                    content: '<p>Welcome to CEPPIC <form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Isneauville"><input type="submit" value="Infos"></form></p>',
                )
            ))

            ->options((new LeafletOptions())
                    ->tileLayer(new TileLayer(
                        url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        options: ['maxZoom' => 19]
                    ))
            );

        $map->addMarker(new Marker(
                position: new Point(49.433331, 1.08333),
                title: 'Ceppic',
                infoWindow: new InfoWindow(
                    content: '<p>Welcome to Rouen <form data-turbo-frame="local_data" method="post"><input type="hidden" name="idElement" value="Rouen"><input type="submit" value="Infos"></form></p>',
                )
            ));

        return $this->render('map/index.html.twig', [
            'map' => $map,
        ]);
    }
}
