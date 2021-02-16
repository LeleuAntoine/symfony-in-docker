<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Game;
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

class CommentCrudController extends AbstractCrudController
{
    private $adminContextProvider;

    public function __construct(AdminContextProvider $adminContextProvider)
    {
        $this->adminContextProvider = $adminContextProvider;
    }

    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title', 'Titre'),
            TextField::new('content', 'Résumé')->onlyWhenUpdating(),
            DateTimeField::new('deletedAt', 'Supprimé')->onlyOnIndex(),
            DateTimeField::new('modificationDate', 'Date de modification')->onlyWhenUpdating(),
            DateTimeField::new('creationDate', 'Date de création')->onlyWhenCreating(),
        ];
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
        $this->addFlash('danger', 'Commentaire supprimé avec succé !');

        return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
    }

    public function cancelSoftDelete()
    {
        $context = $this->getContext();
        $entityManager = $this->getEntityManager($context);

        /**@var $entityInstance Comment */
        $entityInstance = $this->getEntityInstance($context);

        /**@var $game Game */
        $game = $entityInstance->getGame();

        if ($game->getDeletedAt()) {
            $this->addFlash('warning', 'Impossible de restaurer le commentaire. Veuillez restaurer ' . $game->getName());
            return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
        }

        // call of methode deleteEntity with $entityManager and $entityInstance
        $this->restoreEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'Commentaire restauré avec succé !');

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
            ->displayIf(fn(Comment $comment) => $comment->getDeletedAt())
            ->linkToCrudAction('cancelSoftDelete');
        //configure a new action for soft delete
        $softDeleted = Action::new('Supprimer')
            ->displayIf(fn(Comment $comment) => !$comment->getDeletedAt())
            ->linkToCrudAction('softDelete');

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $softDeleted)
            ->add(Crud::PAGE_INDEX, $cancelSoftDeleted);
    }
}