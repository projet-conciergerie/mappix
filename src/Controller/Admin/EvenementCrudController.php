<?php

namespace App\Controller\Admin;

use App\Entity\Evenement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EvenementCrudController extends AbstractCrudController
{
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)      // désactive création
            ->disable(Action::EDIT);     // désactive modification
        // ->disable(Action::DELETE);  // optionnel
    }
    public static function getEntityFqcn(): string
    {
        return Evenement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            IntegerField::new('places', 'Nombre de places'),
            AssociationField::new('category', 'Catégories')
                ->formatValue(function ($value, $entity) {
                    return implode (', ', $value->map(fn($cat) => $cat->getNom())->toArray());
                }),
            AssociationField::new('localisation'),
            DateTimeField::new('start_at' , 'Début'),
            DateTimeField::new('end_at', 'Fin'),
            TextField::new('description'),
        ];
    }

}
