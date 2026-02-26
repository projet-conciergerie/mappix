<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Evenement;
use App\Entity\Localisation;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TourismeDashboardController extends AbstractDashboardController
{
    #[Route('/tourisme', name: 'tourisme_dashboard')]
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
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Evénements', 'fas fa-list', Evenement::class);
        yield MenuItem::linkToCrud('Reservations', 'fas fa-list', Reservation::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-list', Service::class);

        // yield MenuItem::linkToCrud('Localisations', 'fas fa-list', Localisation::class);
    }
}
