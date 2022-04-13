<?php

namespace App\Repository;

use App\Entity\ItemToCraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemToCraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemToCraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemToCraft[]    findAll()
 * @method ItemToCraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemToCraftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemToCraft::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ItemToCraft $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ItemToCraft $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return ItemToCraft[] Returns an array of ItemToCraft objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ItemToCraft
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
