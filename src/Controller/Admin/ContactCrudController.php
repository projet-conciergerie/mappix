<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use Dom\Text;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->disable(Action::NEW);    // désactive création
        // ->disable(Action::EDIT);   // désactive modification
        // ->disable(Action::DELETE);  // optionnel
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email')->hideOnForm(),
            TextField::new('message')->hideOnForm(),
             ChoiceField::new('type')
                ->setChoices([
                    'Commentaire' => 'commentaire',
                    'Réclamation' => 'reclamation',
                ])
        ];
    }

}
