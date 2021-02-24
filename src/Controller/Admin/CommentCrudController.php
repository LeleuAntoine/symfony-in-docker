<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title', 'Titre'),
            TextField::new('title', 'Titre'),
            TextField::new('content', 'Résumé')->onlyWhenUpdating(),
            DateTimeField::new('modificationDate', 'Date de modification')->onlyWhenUpdating(),
            DateTimeField::new('creationDate', 'Date de création')->onlyWhenCreating(),
        ];
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->addFlash('danger', 'Commentaire supprimé avec succès !');
        parent::deleteEntity($entityManager, $entityInstance);
    }


}