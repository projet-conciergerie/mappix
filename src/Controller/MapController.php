<?php

namespace App\Controller;

use App\Service\Overpass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(Request $request, Overpass $overpass, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $csrfTokenManager->getToken('map_form')->getValue();

        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('_token');

            if (!$csrfTokenManager->isTokenValid(new CsrfToken('map_form', $submittedToken))) {
                throw $this->createAccessDeniedException('Token CSRF invalide');
            }

            $idElement = $request->request->get('idElement');
            $category = $request->request->get('category');

            $datas = $overpass->getInArea('Rouen', strtolower($category));

            if (!empty($idElement) && !empty($category)) {
                $data = $datas[$idElement];

                return $this->render('map/_map_details.html.twig', [
                    'category' => $category,
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'website' => $data['website'],
                    'instagram' => $data['instagram'],
                    'facebook' => $data['facebook'],
                    'datas' => $data['tags']
                ]);
            }
        }

        // Toutes les catégories
        $categories = [
            'bars' => [
                'display' => 'Bars',
                'icon' => 'marker_bars.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'bars')
            ],
            'pubs' => [
                'display' => 'Pubs',
                'icon' => 'marker_pubs.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'pubs')
            ],
            'hotels' => [
                'display' => 'Hotels',
                'icon' => 'marker_hotels.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'hotels')
            ],
            'restaurants' => [
                'display' => 'Restaurants',
                'icon' => 'marker_restaurants.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'restaurants')
            ],
            'fontaines' => [
                'display' => 'Fontaines',
                'icon' => 'marker_fontaines.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'fontaines')
            ],
            'toilettes' => [
                'display' => 'Toilettes',
                'icon' => 'marker_toilettes.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'toilettes')
            ],
            'musees' => [
                'display' => 'Musées',
                'icon' => 'marker_musees.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'musees')
            ],
            'monuments' => [
                'display' => 'Monuments',
                'icon' => 'marker_monuments.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'monuments')
            ],
            'parcs' => [
                'display' => 'Parcs',
                'icon' => 'marker_parcs.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'parcs')
            ],
            'monuments_historiques' => [
                'display' => 'Monuments Historiques',
                'icon' => 'marker_monuments_historiques.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'monuments_historiques')
            ],
            'attractions' => [
                'display' => 'Attractions',
                'icon' => 'marker_attractions.png',
                'datas' => $overpass->getInAreaShort('Rouen', 'attractions')
            ],
        ];

        return $this->render('map/index.html.twig', [
            'categories' => $categories,
            'csrf_token' => $token,
        ]);
    }
}
