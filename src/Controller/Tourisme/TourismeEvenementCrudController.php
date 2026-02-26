<?php

namespace App\Controller\Tourisme;

use App\Entity\Evenement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TourismeEvenementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Evenement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            IntegerField::new('places'),
            DateTimeField::new('startAt', 'Date de début'),
            DateTimeField::new('endAt', 'Date de fin'),
            TextareaField::new('description'),
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
}
