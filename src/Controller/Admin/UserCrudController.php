<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private $adminContextProvider;

    public function __construct(AdminContextProvider $adminContextProvider, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->adminContextProvider = $adminContextProvider;
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
            TextField::new('plainPassword', 'Mot de passe')->onlyOnForms(),
            DateTimeField::new('deletedAt', 'Supprimé')->onlyOnIndex()
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
        $date = new \DateTime('now');
        $entityInstance->setDeletedAt($date);
        $entityManager->flush();
    }

    public function restoreEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setDeletedAt(null);
        $entityManager->flush();
    }

    public function softDelete()
    {
        $context = $this->getContext();
        $entityManager = $this->getEntityManager($context);
        $entityInstance = $this->getEntityInstance($context);

        // call of methode deleteEntity with $entityManager and $entityInstance
        $this->deleteEntity($entityManager, $entityInstance);
        $this->addFlash('danger', 'Utilisateur supprimé avec succé !');

        return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
    }

    public function cancelSoftDelete()
    {
        $context = $this->getContext();
        $entityManager = $this->getEntityManager($context);
        $entityInstance = $this->getEntityInstance($context);

        // call of methode deleteEntity with $entityManager and $entityInstance
        $this->restoreEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Utilisateur restauré avec succé !');

        return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
    }

    public function getContext()
    {
        return $this->adminContextProvider->getContext();
    }

    public function getEntityManager($context)
    {
        return $this->get('doctrine')->getManagerForClass($context->getEntity()->getFqcn());
    }

    public function getEntityInstance($context)
    {
        $entityInstance = $context->getEntity()->getInstance();
        $event = new BeforeEntityDeletedEvent($entityInstance);
        $this->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }
        return $event->getEntityInstance();
    }

    public function configureActions(Actions $actions): Actions
    {
        //set a new action for cancel a soft delete
        $cancelSoftDeleted = Action::new('Annuler la Suppréssion')
            ->displayIf(fn(User $user) => $user->getDeletedAt())
            ->linkToCrudAction('cancelSoftDelete');

        //set a new action for soft delete
        $softDeleted = Action::new('Suppréssion')
            ->displayIf(fn(User $user) => !$user->getDeletedAt())
            ->linkToCrudAction('softDelete');

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $softDeleted)
            ->add(Crud::PAGE_INDEX, $cancelSoftDeleted);
    }
}