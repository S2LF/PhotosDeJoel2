<?php

namespace App\Repository;

use App\Entity\Actuality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actuality>
 *
 * @method Actuality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actuality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actuality[]    findAll()
 * @method Actuality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActualityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actuality::class);
    }

    public function save(Actuality $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Actuality $entity, bool $flush = false): void
    {
        // Make a soft delete
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
			return $this->createQueryBuilder('a')
                ->orderBy('a.position', 'ASC')
                ->where('a.deletedAt IS NULL')
				->getQuery()
				->getResult()
			;
    }

    public function findAllOrderByPosDeleted()
    {   
        return $this->createQueryBuilder('a')
            ->orderBy('a.position', 'ASC')
            ->where('a.deletedAt IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }
}
