<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id');
        $name = TextField::new('name');
        $rarity = TextField::new('rarity');
        $lvlItem = NumberField::new('lvlItem');
        $avgPrice = NumberField::new('averagePrice');

        return [
            $id,
            $name,
            $rarity,
            $lvlItem,
            $avgPrice,
        ];
    }
}
