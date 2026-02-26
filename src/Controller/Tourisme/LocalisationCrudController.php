<?php

namespace App\Controller\Tourisme;

use App\Entity\Localisation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LocalisationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Localisation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            TextField::new('adresse'),
            TextField::new('telephone'),
            TextField::new('email'),
            ChoiceField::new('position')->setChoices([
                'Gauche' => 'gauche',
                'Droite' => 'droite',
                'Haut' => 'haut',
                'Bas' => 'bas',
            ])
            ];
    }
}
