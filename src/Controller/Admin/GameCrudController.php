<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class GameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            TextField::new('resume', 'Résumé'),
            TextField::new('materialRequired', 'Matériels requis'),
            IntegerField::new('download', 'Nombre de téléchargements'),
            TextField::new('photo', 'Photo (URL)'),
            DateTimeField::new('modificationDate', 'Date de modification')->onlyWhenUpdating(),
            DateTimeField::new('creationDate', 'Date de création')->onlyWhenCreating(),
        ];
    }
}