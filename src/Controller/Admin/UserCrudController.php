<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function __construct(public UserPasswordHasherInterface $userPasswordHasher)
    {
        
    }
    

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $user = new User();
        $user = $entityInstance;
        
        $entityInstance->setPassword($this->userPasswordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $user = new User();
        $user = $entityInstance;
        $entityInstance->setPassword($this->userPasswordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));
        parent::updateEntity($entityManager, $entityInstance);
        $user->setPassword("");
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('userName'),
            EmailField::new('email'),
            TextField::new('password'),
            ChoiceField::new('roles', 'Roles')
                ->allowMultipleChoices()
                ->autocomplete()
                ->setChoices([
                    'ADMINISTRATEUR' => 'ROLE_ADMIN',
                    'GESTIONNAIRE LIGUE' => 'ROLE_ADMIN_LIGUE',
                    'GESTIONNAIRE CLUB' => 'ROLE_ADMIN_CLUB',
                    'INVITE' => 'ROLE_GUEST',
                ]),
            //BooleanField::new('isVerified')
        ];
    }
}
