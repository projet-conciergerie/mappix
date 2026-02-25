<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\EvenementRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/reservation/{id}', name: 'app_reservation_create')]
    public function create(int $id, EvenementRepository $evenementRepository, ReservationRepository $reservationRepository, EntityManagerInterface $em): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $evenement = $evenementRepository->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        // Vérifier si déjà réservé
        $existingReservation = $reservationRepository
            ->findOneByUserAndEvenement($user, $evenement);

        if ($existingReservation) {
            $this->addFlash('error', 'Vous avez déjà réservé cet événement.');
            return $this->redirectToRoute('app_evenement');
        }

        // Vérifier places restantes
        if ($evenement->getPlacesRestantes() <= 0) {
            $this->addFlash('error', 'Il n’y a plus de places disponibles.');
            return $this->redirectToRoute('app_evenement');
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setEvenement($evenement);

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Réservation confirmée !');

        return $this->redirectToRoute('app_evenement');
    }

    #[Route('/reservation', name: 'my_reservation_list')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/reservation/{id}/delete', name: 'app_reservation_delete', methods: ['POST'])]
public function delete(Reservation $reservation, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

    // Sécurité : vérifier que l'utilisateur est propriétaire
    if ($reservation->getUser() !== $this->getUser()) {
        $this->addFlash('error', 'Vous ne pouvez pas annuler cette réservation.');
        return $this->redirectToRoute('my_reservation_list');
    }

    $em->remove($reservation);
    $em->flush();

    $this->addFlash('success', 'Réservation annulée avec succès !');

    return $this->redirectToRoute('my_reservation_list');
}
}
