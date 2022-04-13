<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Filter\ItemFilter;
use App\Entity\InventoryItems;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class InventoryItemsCrudController extends AbstractCrudController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

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
            ->overrideTemplate('crud/index', 'admin/inventoryItems/index.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id');
        $item = AssociationField::new('item')->autocomplete();
        $inventory = AssociationField::new('inventory');
        $quantity = IntegerField::new('quantity');

        if (Crud::PAGE_INDEX === $pageName) {
            return [
                $id,
                $item,
                $inventory,
                $quantity,
            ];
        }

        return [
            $item,
            $inventory,
            $quantity,
        ];
    }

    /**
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ItemFilter::new('item'));
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var InventoryItems $inventoryItemPrePersist */
        $inventoryItemPrePersist = $entityInstance;
        /** @var InventoryItems $inventoryItem */
        $inventoryItem = $this->em->getRepository(InventoryItems::class)->findOneBy(["item" => $inventoryItemPrePersist->getItem(), "inventory" => $inventoryItemPrePersist->getInventory()]);
        if ($inventoryItem) {
            $inventoryItem->setQuantity($inventoryItem->getQuantity() + $inventoryItemPrePersist->getQuantity());
            $entityInstance = $inventoryItem;
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
