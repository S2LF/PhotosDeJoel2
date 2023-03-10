<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Link>
 *
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function save(Link $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Link $entity, bool $flush = false): void
    {
        $entity->setDeletedAt(new \DateTime());
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function findAllOrderByPos()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.position', 'ASC')
            ->where('l.deletedAt IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllOrderByPosDeleted()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.position', 'ASC')
            ->where('l.deletedAt IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }
}
