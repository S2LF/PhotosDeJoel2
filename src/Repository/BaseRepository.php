<?php

namespace App\Repository;

use App\Entity\Base;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Base>
 *
 * @method Base|null find($id, $lockMode = null, $lockVersion = null)
 * @method Base|null findOneBy(array $criteria, array $orderBy = null)
 * @method Base[]    findAll()
 * @method Base[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Base::class);
    }

    public function save(Base $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Base $entity, bool $flush = false): void
    {
        $entity->setDeletedAt(new \DateTime());
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        // $this->getEntityManager()->remove($entity);

        // if ($flush) {
        //     $this->getEntityManager()->flush();
        // }
    }
}
