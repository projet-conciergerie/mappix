<?php

namespace App\Controller\Tourisme;

use App\Entity\Category;
use App\Entity\Evenement;
use App\Entity\Localisation;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/tourisme', routeName: 'tourisme')]
#[IsGranted('ROLE_TOURISME')]
class TourismeDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('tourisme_dashboard/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Office de Tourisme');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');
        yield MenuItem::linkToRoute('Utilisateurs', 'fas fa-list', 'tourisme_user_index');
        yield MenuItem::linkToRoute('Evènements', 'fas fa-list', 'tourisme_evenement_index');
        yield MenuItem::linkToRoute('Réservations', 'fas fa-list', 'tourisme_reservation_index');
        yield MenuItem::linkToRoute('Services', 'fas fa-list', 'tourisme_service_index');
    }
}
