<?php

namespace App\Controller\Admin;

use App\Entity\ItemToCraft;
use App\Entity\Sale;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ItemToCraftCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemToCraft::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id');
        $item = AssociationField::new('item')->autocomplete();
        $user = AssociationField::new('user');

        return [
            $item,
            $user,
        ];
    }
}
