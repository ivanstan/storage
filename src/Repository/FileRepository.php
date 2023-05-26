<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function save(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        (new Filesystem())->remove($entity->getDestination());

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search($filters = []): QueryBuilder
    {
        $builder = $this->createQueryBuilder('f')
            ->select('f')
            ->leftJoin('f.nodes', 'n')
            ->orderBy('f.created_at', Criteria::DESC);

        if (isset($filters['nodes']) && ($filters['nodes'] === 'null')) {
            $builder->where($builder->expr()->isNull('n.id'));
        }

        return $builder;
    }

    public function findOneBySha256($value): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.sha256 = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
