<?php

namespace App\Controller\Admin;

use App\Entity\InventoryItems;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class InventoryItemsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InventoryItems::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/index', 'admin/inventoryItems/index.html.twig')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        $item = AssociationField::new('item');
        $inventory = AssociationField::new('inventory')->autocomplete();
        $quantity = IntegerField::new('quantity');

        return [
            $item,
            $inventory,
            $quantity,
        ];
    }
}
