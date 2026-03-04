<?php

namespace App\Controller;

use App\Service\Overpass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use function PHPUnit\Framework\isNumeric;

final class MapController extends AbstractController
{
    private $categories = [
        'bars' => [
            'display' => 'Bars',
            'icon' => 'marker_bars.png'
        ],
        'pubs' => [
            'display' => 'Pubs',
            'icon' => 'marker_pubs.png'
        ],
        'hotels' => [
            'display' => 'Hotels',
            'icon' => 'marker_hotels.png'
        ],
        'restaurants' => [
            'display' => 'Restaurants',
            'icon' => 'marker_restaurants.png'
        ],
        'fontaines' => [
            'display' => 'Fontaines',
            'icon' => 'marker_fontaines.png'
        ],
        'toilettes' => [
            'display' => 'Toilettes',
            'icon' => 'marker_toilettes.png'
        ],
        'musees' => [
            'display' => 'Musées',
            'icon' => 'marker_musees.png'
        ],
        'monuments' => [
            'display' => 'Monuments',
            'icon' => 'marker_monuments.png'
        ],
        'parcs' => [
            'display' => 'Parcs',
            'icon' => 'marker_parcs.png'
        ],
        'monuments_historiques' => [
            'display' => 'Monuments Historiques',
            'icon' => 'marker_monuments_historiques.png'
        ],
        'attractions' => [
            'display' => 'Attractions',
            'icon' => 'marker_attractions.png'
        ]
    ];

    #[Route('/map', name: 'app_map', methods: ['GET'])]
    public function index(Request $request, Overpass $overpass, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $csrfTokenManager->getToken('map_form')->getValue();

        // Toutes les catégories a afficher sur la carte
        $shownCategories = $this->categories;
        $shownCategories['bars']['datas'] = $overpass->getInAreaShort('Rouen', 'bars');
        $shownCategories['pubs']['datas'] = $overpass->getInAreaShort('Rouen', 'pubs');
        $shownCategories['hotels']['datas'] = $overpass->getInAreaShort('Rouen', 'hotels');
        $shownCategories['restaurants']['datas'] = $overpass->getInAreaShort('Rouen', 'restaurants');
        $shownCategories['fontaines']['datas'] = $overpass->getInAreaShort('Rouen', 'fontaines');
        $shownCategories['toilettes']['datas'] = $overpass->getInAreaShort('Rouen', 'toilettes');
        $shownCategories['musees']['datas'] = $overpass->getInAreaShort('Rouen', 'musees');
        $shownCategories['monuments']['datas'] = $overpass->getInAreaShort('Rouen', 'monuments');
        $shownCategories['parcs']['datas'] = $overpass->getInAreaShort('Rouen', 'parcs');
        $shownCategories['monuments_historiques']['datas'] = $overpass->getInAreaShort('Rouen', 'monuments_historiques');
        $shownCategories['attractions']['datas'] = $overpass->getInAreaShort('Rouen', 'attractions');

        return $this->render('map/index.html.twig', [
            'categories' => $shownCategories,
            'csrf_token' => $token,
        ]);
    }

    #[Route('/map/data', name: 'app_map_data', methods: ['POST'])]
    public function getData(Request $request, Overpass $overpass, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $csrfTokenManager->getToken('map_form')->getValue();

        if (!$request->isMethod('POST')) {
            return new Response('Method Not Allowed', 405);
        }

        $submittedToken = $request->request->get('_token');

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('map_form', $submittedToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        // Récupérer les paramètres de la requête
        $idElement = trim(htmlspecialchars($request->request->get('idElement')));
        $category = trim(htmlspecialchars($request->request->get('category')));

        // Valider les paramètres
        if (!isNumeric($idElement)) {
            return new Response('Bad Request', 400);
        }

        if (!array_key_exists($category, $this->categories)) {
            return new Response('Bad Request', 400);
        }

        $datas = $overpass->getInArea('Rouen', strtolower($category));

        if (!isset($datas[$idElement])) {
            return new Response('Bad Request', 400);
        }

            $data = $datas[$idElement];

            $thumbnail = null;
            if (isset($data['thumbnail'])) {
                $thumbnail = $data['thumbnail'];
            } else {
                $wikidata = $data['wikidata'] ?? null;
                if ($wikidata) {
                    $thumbnail = $overpass->getWikidataThumbnail($wikidata);
                }
            }

            return $this->render('map/_map_details.html.twig', [
                'category' => $this->categories[$category]['display'] ?? $category,
                'description' => $data['description'],
                'name' => $data['name'],
                'address' => $data['address'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'website' => $data['website'],
                'instagram' => $data['instagram'],
                'facebook' => $data['facebook'],
                'twitter' => $data['twitter'],
                'wikipedia' => $data['wikipedia'],
                'openhours' => $data['openhours'],
                'thumbnail' => $thumbnail,
                'datas' => $data['tags']
            ]);
    }

    #[Route('/map/goto', name: 'app_map_goto', methods: ['GET'])]
    public function goto(Request $request, Overpass $overpass, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $csrfTokenManager->getToken('map_form')->getValue();

        if (!$request->isMethod('GET')) {
            return new Response('Method Not Allowed', 405);
        }

        // Toutes les catégories a afficher sur la carte
        $shownCategories = $this->categories;
        $shownCategories['bars']['datas'] = $overpass->getInAreaShort('Rouen', 'bars');
        $shownCategories['pubs']['datas'] = $overpass->getInAreaShort('Rouen', 'pubs');
        $shownCategories['hotels']['datas'] = $overpass->getInAreaShort('Rouen', 'hotels');
        $shownCategories['restaurants']['datas'] = $overpass->getInAreaShort('Rouen', 'restaurants');
        $shownCategories['fontaines']['datas'] = $overpass->getInAreaShort('Rouen', 'fontaines');
        $shownCategories['toilettes']['datas'] = $overpass->getInAreaShort('Rouen', 'toilettes');
        $shownCategories['musees']['datas'] = $overpass->getInAreaShort('Rouen', 'musees');
        $shownCategories['monuments']['datas'] = $overpass->getInAreaShort('Rouen', 'monuments');
        $shownCategories['parcs']['datas'] = $overpass->getInAreaShort('Rouen', 'parcs');
        $shownCategories['monuments_historiques']['datas'] = $overpass->getInAreaShort('Rouen', 'monuments_historiques');
        $shownCategories['attractions']['datas'] = $overpass->getInAreaShort('Rouen', 'attractions');

        // Récupérer les paramètres de la requête
        $idElement = trim(htmlspecialchars($request->query->get('id')));
        $category = trim(htmlspecialchars($request->query->get('category')));

        // Valider les paramètres
        if (!isNumeric($idElement)) {
            return new Response('Bad Request', 400);
        }

        if (!isset($shownCategories[$category])) {
            return new Response('Bad Request', 400);
        }

        if (!isset($shownCategories[$category]['datas'][$idElement])) {
            return new Response('Bad Request', 400);
        }

        // Si les paramètres sont valides, rediriger vers la carte avec les données nécessaires pour centrer la carte sur l'élément recherché
        $data = $shownCategories[$category]['datas'][$idElement];

        return $this->render('map/index.html.twig', [
            'position' => [
                'category' => $category,
                'lat' => $data['lat'],
                'lon' => $data['lon']
            ],
            'categories' => $shownCategories,
            'csrf_token' => $token,
        ]);
    }
}
