<?php

namespace App\Repository;

use App\Entity\CategoryPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryPhoto>
 *
 * @method CategoryPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryPhoto[]    findAll()
 * @method CategoryPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryPhoto::class);
    }

    public function save(CategoryPhoto $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategoryPhoto $entity, bool $flush = false): void
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
        return $this->createQueryBuilder('cp')
            ->where('cp.deletedAt IS NULL')
            ->orderBy('cp.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
