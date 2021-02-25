<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GameCrudController extends AbstractCrudController
{
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
            DateTimeField::new('modificationDate', 'Date de modification')->onlyWhenUpdating(),
            DateTimeField::new('creationDate', 'Date de création')->onlyWhenCreating(),
        ];
        if (Crud::PAGE_INDEX == $pageName) {
            $fields[] = $image;
        } else {
            $fields[] = $imageFile;
        }

        return $fields;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->addFlash('danger', 'Jeu supprimé avec succès !');
        parent::deleteEntity($entityManager, $entityInstance);
    }
}
