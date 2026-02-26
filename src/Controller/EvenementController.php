<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EvenementController extends AbstractController
{
    // #[Route('/evenement', name: 'app_evenement')]
    // public function index(EvenementRepository $evenementRepository): Response
    // {
    //     return $this->render('evenement/index.html.twig', [
    //         'evenements' => $evenementRepository->findAll(),
    //     ]);
    // }
    #[Route('/api/evenements', name: 'api_evenements')]
    public function api(EvenementRepository $repo): JsonResponse
    {
        $events = [];

        foreach ($repo->findAll() as $evenement) {

            $events[] = [
                'id' => $evenement->getId(),
                'title' => $evenement->getNom(),
                'start' => $evenement->getStartAt()->format('Y-m-d H:i:s'),
                'end' => $evenement->getEndAt()->format('Y-m-d H:i:s'),
                'url' => $this->generateUrl('app_evenement_show', [
                    'id' => $evenement->getId()
                ]),
                'backgroundColor' => $evenement->getPlacesRestantes() > 0 ? '#32CD32' : '#FF4C4C',
            ];
        }

        return $this->json($events);
    }
    #[Route('/evenements', name: 'app_evenements')]
    public function calendrier(): Response
    {
        return $this->render('evenement/calendar.html.twig');
    }
    #[Route('/evenement/{id}', name: 'app_evenement_show')]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}
