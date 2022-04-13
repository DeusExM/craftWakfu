<?php

namespace App\Repository;

use App\Entity\Inventory;
use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findCraftableItems(array $items)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.recipe' , 'r')
            ->innerJoin('r.ingredients' , 'igr')
            ->where('igr.item IN (:items)')
            ->setParameter('items', $items)
            ->orderBy('i.lvlItem', 'DESC')
            ->addOrderBy('i.rarity', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findItemSameName(Item $item)
    {
        return $this->createQueryBuilder('i')
            ->where('i.name = :name')
            ->andWhere("i.rarity != 'Ancien objet'")
            ->andWhere("i.rarity != 'Souvenir'")
            ->andWhere("i.recipe IS NOT NULL")
            ->setParameter('name', $item->getName())
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Item
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
