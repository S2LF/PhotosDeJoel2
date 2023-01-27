<?php

namespace App\Repository;

use App\Entity\Exposition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exposition>
 *
 * @method Exposition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exposition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exposition[]    findAll()
 * @method Exposition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exposition::class);
    }

    public function save(Exposition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Exposition $entity, bool $flush = false): void
    {
        $entity->setDeletedAt(new \DateTime());
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        // $this->getEntityManager()->remove($entity);

        // if ($flush) {
        //     $this->getEntityManager()->flush();
        // }
    }
    
    public function findAllOrderByPos()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.position', 'ASC')
            ->where('e.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByPosDeleted()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.position', 'ASC')
            ->where('e.deletedAt IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}
