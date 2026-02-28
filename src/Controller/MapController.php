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
            'Bars' => $overpass->getInArea('Rouen', 'bars'),
            'Pubs' => $overpass->getInArea('Rouen', 'pubs'),
            'Hotels' => $overpass->getInArea('Rouen', 'hotels'),
            'Restaurants' => $overpass->getInArea('Rouen', 'restaurants'),
            'Fontaines' => $overpass->getInArea('Rouen', 'fontaines'),
            'Toilettes' => $overpass->getInArea('Rouen', 'toilettes'),
            'Musees' => $overpass->getInArea('Rouen', 'musees'),
            'Monuments' => $overpass->getInArea('Rouen', 'monuments'),
            'Parcs' => $overpass->getInArea('Rouen', 'parcs'),
            'Monuments Historiques' => $overpass->getInArea('Rouen', 'monuments_historiques'),
            'Attractions' => $overpass->getInArea('Rouen', 'attractions')
        ];

        return $this->render('map/index.html.twig', [
            'categories' => $categories,
            'csrf_token' => $token,
        ]);
    }
}
