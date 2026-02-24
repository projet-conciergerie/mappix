<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class ReservationCrudController extends AbstractCrudController
{
    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->disable(Action::NEW)    // désactive création
        ->disable(Action::EDIT);   // désactive modification
        // ->disable(Action::DELETE);  // optionnel
    }
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),

            // Affiche le nom de l'événement
            AssociationField::new('evenement')
                ->setLabel('Événement'),

            // Affiche l’utilisateur
            AssociationField::new('user')
                ->setLabel('Utilisateur'),

            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
