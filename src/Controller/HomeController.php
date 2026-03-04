<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use App\Repository\ServiceRepository;
use App\Service\Overpass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private function renderAcceuil(
        EvenementRepository $evenementRepository,
        ServiceRepository $serviceRepository,
        Overpass $overpass,
        int $quantity
    ) {
        // Récupérer les 3 derniers événements à venir
        $recentEvents = $evenementRepository->findBy(
            [],
            ['startAt' => 'ASC'],
            3
        );

        // ========================================
        // Récupérer les services depuis Overpass
        // ========================================

        // Définir les catégories à afficher
        $categories = [
            'bars' => 'Bars',
            'pubs' => 'Pubs',
            'hotels' => 'Hôtels',
            'restaurants' => 'Restaurants',
            'musees' => 'Musées',
            'monuments' => 'Monuments',
            'parcs' => 'Parcs',
            'attractions' => 'Attractions',
        ];

        $allServices = [];

        // Récupérer les services de toutes les catégories
        foreach ($categories as $categoryKey => $categoryDisplay) {
            $servicesData = $overpass->getInArea('Rouen', $categoryKey);

            foreach ($servicesData as $id => $service) {
                // Ne garder que les services avec un nom
                if (empty($service['name'])) {
                    continue;
                }

                $description = $service['description'];

                // Si pas de description, créer une description générique
                if (!$description) {
                    $description = $this->generateDescription($categoryKey, $service['name']);
                }

                // Limiter la description à 150 caractères
                if (strlen($description) > 150) {
                    $description = substr($description, 0, 150) . '...';
                }

                $allServices[] = [
                    'id' => $id,
                    'nom' => $service['name'],
                    'description' => $description,
                    'category' => $categoryDisplay,
                    'categoryKey' => $categoryKey,
                    'address' => $service['address'] ?: 'Adresse non disponible',
                    'phone' => $service['phone'],
                    'email' => $service['email'],
                    'website' => $service['website'],
                    'lat' => $service['lat'],
                    'lon' => $service['lon'],
                ];
            }
        }

        // Trier par nom (ASC)
        usort($allServices, function ($a, $b) {
            return strcmp($a['nom'], $b['nom']);
        });

        // Sélectionner 3 services aléatoirement
        $randomServices = [];
        if (count($allServices) > 0) {
            // Mélanger le tableau
            shuffle($allServices);

            // Prendre les $quantity premiers (ou moins si pas assez de services)
            $randomServices = array_slice($allServices, 0, min($quantity, count($allServices)));

            // Re-trier les $quantity sélectionnés par nom (ASC)
            usort($randomServices, function ($a, $b) {
                return strcmp($a['nom'], $b['nom']);
            });
        }

        return $this->render('home/index.html.twig', [
            'recentEvents' => $recentEvents,
            'services' => $randomServices, // Services depuis Overpass
        ]);
    }

    #[Route('/actu', name: 'app_actu')]
    public function actu(
        EvenementRepository $evenementRepository,
        ServiceRepository $serviceRepository,
        Overpass $overpass
    ) {
        return $this->renderAcceuil($evenementRepository, $serviceRepository, $overpass, 6);
    }

    #[Route('/', name: 'app_home')]
    public function index(
        EvenementRepository $evenementRepository,
        ServiceRepository $serviceRepository,
        Overpass $overpass
    ): Response {

        // Rediriger vers /map si l'utilisateur est connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_map');
        }

        return $this->renderAcceuil($evenementRepository, $serviceRepository, $overpass, 3);
    }

    /**
     * Génère une description générique basée sur la catégorie
     */
    private function generateDescription(string $category, string $name): string
    {
        $descriptions = [
            'bars' => "Bar situé à Rouen proposant une ambiance conviviale et une sélection de boissons.",
            'pubs' => "Pub accueillant offrant une atmosphère chaleureuse et des bières de qualité.",
            'hotels' => "Établissement hôtelier situé à Rouen, idéal pour votre séjour dans la ville.",
            'restaurants' => "Restaurant proposant une cuisine de qualité dans un cadre agréable.",
            'fontaines' => "Point d'eau potable accessible au public.",
            'toilettes' => "Toilettes publiques disponibles.",
            'musees' => "Musée culturel présentant des collections et expositions.",
            'monuments' => "Monument historique emblématique de Rouen.",
            'parcs' => "Espace vert et de détente pour profiter de la nature en ville.",
            'monuments_historiques' => "Site historique classé, témoin du patrimoine de Rouen.",
            'attractions' => "Attraction touristique incontournable à découvrir lors de votre visite.",
        ];

        return $descriptions[$category] ?? "Lieu d'intérêt situé à Rouen.";
    }
}
