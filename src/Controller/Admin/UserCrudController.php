<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('email', 'Email'),
            TextField::new('username', 'Pseudo')->onlyOnForms(),
            TextField::new('name', 'Nom')->onlyOnForms(),
            TextField::new('lastname', 'Prénom')->onlyOnForms(),
            TextField::new('street', 'Rue')->onlyOnForms(),
            TextField::new('additional_address', 'Complément d\'adresse')->onlyOnForms(),
            TextField::new('postal_code', 'Code Postal')->onlyOnForms(),
            TextField::new('city', 'Ville')->onlyOnForms(),
            TextField::new('plainPassword', 'Mot de passe')->onlyOnForms(),
            DateTimeField::new('deletedAt', 'Supprimé')->onlyOnIndex(),
        ];
    }

    protected function setEncodedPassword($entityInstance): void
    {
        /** @var User $entityInstance */
        if (method_exists($entityInstance, 'setPassword') && $entityInstance->getPlainPassword()) {
            $encodedPassword = $this->passwordEncoder->encodePassword($entityInstance, $entityInstance->getPlainPassword());
            $entityInstance->setPassword($encodedPassword);
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(
            ['validation_groups' => ['Default', 'new']],
            ['validation_groups' => ['Default']]
        );
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->setEncodedPassword($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->setEncodedPassword($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->addFlash('danger', 'Utilisateur supprimé avec succès !');
        parent::deleteEntity($entityManager, $entityInstance);
    }
}