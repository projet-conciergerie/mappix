<?php

namespace App\Controller\Tourisme;

use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Service::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            TextField::new('hours'),
            TextField::new('description'),
            BooleanField::new('pmr'),
            AssociationField::new('localisation')
                ->renderAsEmbeddedForm()
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
            AssociationField::new('category', 'Catégories')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $value->map(fn($cat) => $cat->getNom())->toArray());
                })
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ->disable(Action::NEW)     // désactive création
            ->disable(Action::EDIT)    // désactive modification
            ->disable(Action::DELETE); // optionnel
    }
}
