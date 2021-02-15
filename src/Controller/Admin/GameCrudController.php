<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GameCrudController extends AbstractCrudController
{
    private $adminContextProvider;

    public function __construct(AdminContextProvider $adminContextProvider)
    {
        $this->adminContextProvider = $adminContextProvider;
    }

    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $imageFile = TextField::new('pictureFile', 'Photo')->setFormType(VichImageType::class);
        $image = ImageField::new('picture', 'Photo')->setBasePath('/upload/image');

        $fields = [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Nom'),
            TextField::new('resume', 'Résumé')->onlyWhenUpdating(),
            TextField::new('materialRequired', 'Matériels requis')->onlyWhenUpdating(),
            IntegerField::new('download', 'Nombre de téléchargements'),
//            TextField::new('pictureFile', 'Photo (URL)')
//                ->setFormType(VichImageType::class)->hideOnIndex(),
            DateTimeField::new('deletedAt', 'Supprimé')->onlyOnIndex(),
            DateTimeField::new('modificationDate', 'Date de modification')->onlyWhenUpdating(),
            DateTimeField::new('creationDate', 'Date de création')->onlyWhenCreating(),
        ];
        if ($pageName == Crud::PAGE_INDEX) {
            $fields[] = $image;
        } else {
            $fields[] = $imageFile;
        }

        return $fields;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $date = new \DateTime('now');
        $entityInstance->setDeletedAt($date);
        $comments = $entityInstance->getComments();
        foreach ($comments as $comment) {
            $comment->setDeletedAt($date);
        }
        $entityManager->flush();
    }

    public function restoreEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setDeletedAt(null);
        $comments = $entityInstance->getComments();
        foreach ($comments as $comment) {
            $comment->setDeletedAt(null);
        }
        $entityManager->flush();
    }

    public function softDelete()
    {
        $context = $this->getContext();
        $entityManager = $this->getEntityManager($context);
        $entityInstance = $this->getEntityInstance($context);

        // call of methode deleteEntity with $entityManager and $entityInstance
        $this->deleteEntity($entityManager, $entityInstance);
        $this->addFlash('danger', 'Jeu et commentaires supprimé avec succé !');

        return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
    }

    public function cancelSoftDelete()
    {
        $context = $this->getContext();
        $entityManager = $this->getEntityManager($context);
        $entityInstance = $this->getEntityInstance($context);

        // call of methode deleteEntity with $entityManager and $entityInstance
        $this->restoreEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Jeu et commentaires restauré avec succé !');

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
            ->displayIf(fn(Game $game) => $game->getDeletedAt())
            ->linkToCrudAction('cancelSoftDelete');
        //configure a new action for soft delete
        $softDeleted = Action::new('Soft Delete')
            ->displayIf(fn(Game $game) => !$game->getDeletedAt())
            ->linkToCrudAction('softDelete');

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $softDeleted)
            ->add(Crud::PAGE_INDEX, $cancelSoftDeleted);
    }
}