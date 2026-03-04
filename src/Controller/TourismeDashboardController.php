<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Evenement;
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
            ->setTitle('Office de Tourisme - Rouen');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');
        
        yield MenuItem::section('Gestion');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Réservations', 'fas fa-calendar-check', Reservation::class);
        
        yield MenuItem::section('Contenu');
        yield MenuItem::linkToCrud('Événements', 'fas fa-calendar-alt', Evenement::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-concierge-bell', Service::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-folder', Category::class);
        
        yield MenuItem::section('Navigation');
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-arrow-left', 'app_home');
    }
}
