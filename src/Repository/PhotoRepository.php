<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function save(Photo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Photo $entity, bool $flush = false): void
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
        return $this->createQueryBuilder('p')
            ->orderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPhotoCatByPos($catId)
    {
        return $this->createQueryBuilder('p')
          ->where('p.categoryPhoto = ' . $catId)
          ->orderBy('p.position', 'ASC')
          ->andWhere('p.deletedAt IS NULL')
          ->getQuery()
          ->getResult();
    }

    public function getPhotoCatByPosDeleted($catId)
    {
        return $this->createQueryBuilder('p')
          ->where('p.categoryPhoto = ' . $catId)
          ->orderBy('p.position', 'ASC')
          ->andWhere('p.deletedAt IS NOT NULL')
          ->getQuery()
          ->getResult();
    }
}
