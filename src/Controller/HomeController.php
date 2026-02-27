<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        EvenementRepository $evenementRepository,
        ServiceRepository $serviceRepository
    ): Response {
        // Récupérer les 3 derniers événements à venir
        $recentEvents = $evenementRepository->findBy(
            [],
            ['startAt' => 'ASC'], // Adapte selon le nom exact de ton champ date
            3
        );

        // Récupérer les 3 services (ou ceux mis en avant)
        $services = $serviceRepository->findBy(
            [],
            ['createdAt' => 'DESC'], // Adapte selon tes besoins
            3
        );

        return $this->render('home/index.html.twig', [
            'recentEvents' => $recentEvents,
            'services' => $services,
        ]);
    }
}