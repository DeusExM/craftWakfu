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

    public function findCraftableObject(Inventory $inventory)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.ingredients' , 'ing')
            ->innerJoin('i.inventoryItems' , 'iit')
            ->innerJoin('iit.inventory' , 'inv')
            ->andWhere('iit.inventory = :inventory')
            ->setParameter('inventory', $inventory)
            ->orderBy('i.id', 'ASC')
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
