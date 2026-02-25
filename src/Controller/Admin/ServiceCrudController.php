<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
            AssociationField::new('category', 'Catégories')
                ->formatValue(function ($value, $entity) {
                    return implode (', ', $value->map(fn($cat) => $cat->getNom())->toArray());
                }),
            TextField::new('description'),
            BooleanField::new('pmr', 'PMR'),
            AssociationField::new('localisation')
                ->renderAsEmbeddedForm()
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
        ];
    }
}
