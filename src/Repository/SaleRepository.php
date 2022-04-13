<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SaleRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleRepository[]    findAll()
 * @method SaleRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SaleRepository $entity, bool $flush = true): void
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
    public function remove(SaleRepository $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
