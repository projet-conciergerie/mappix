<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\Inflector\Language;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) return;

        if (!in_array('ROLE_ADMIN', $entityInstance->getRoles())) {
            throw new \Exception('Un administrateur doit garder au moins un ROLE_ADMIN.');
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW);      // désactive création
        // ->disable(Action::EDIT);     // désactive modification
        // ->disable(Action::DELETE);  // optionnel
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email')->hideOnForm(),
            ArrayField::new('roles')->hideOnForm(),
            TextField::new('nom')->hideOnForm(),
            TextField::new('prenom')->hideOnForm(),
            TextField::new('pseudo')->hideOnForm(),
            TextField::new('langue')->hideOnForm(),
            DateTimeField::new('lastConnectedAt')->hideOnForm(),
            ChoiceField::new('roles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->allowMultipleChoices()
                ->renderExpanded(true)
                ->onlyOnForms() // n’apparaît que dans le formulaire d’édition
        ];
    }
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) return;

        // On force à ne mettre à jour que les rôles
        $userFromDb = $entityManager->getRepository(User::class)->find($entityInstance->getId());
        $userFromDb->setRoles($entityInstance->getRoles());

        $entityManager->flush();
    }
}
