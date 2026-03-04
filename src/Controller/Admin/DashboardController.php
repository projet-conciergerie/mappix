<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Category;
use App\Entity\Contact;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Mappix');
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToRoute('Service', 'fas fa-list', 'admin_service_index');
        yield MenuItem::linkToRoute('User', 'fas fa-list', 'admin_user_index');
        yield MenuItem::linkToRoute('Reservation', 'fas fa-list', 'admin_reservation_index');
        yield MenuItem::linkToRoute('Evenement', 'fas fa-list', 'admin_evenement_index');
        yield MenuItem::linkToRoute('Contact', 'fas fa-list', 'admin_contact_index');
        yield MenuItem::linkToRoute('Category', 'fas fa-list', 'admin_category_index');
        yield MenuItem::linkToRoute('Avis', 'fas fa-list', 'admin_avis_index');
    }
}
