<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Entity\Sale;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SaleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sale::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id');
        $item = AssociationField::new('item')->autocomplete();
        $user = AssociationField::new('user');
        $tax = NumberField::new('tax');
        $sale = NumberField::new('sale');
        $extraCost = NumberField::new('extraCost');
        $saleAt = DateField::new('saleAt');
        $forSaleAt = DateField::new('forSaleAt');

        return [
            $item,
            $user,
            $tax,
            $sale,
            $extraCost,
            $forSaleAt,
            $saleAt,
        ];
    }
}
