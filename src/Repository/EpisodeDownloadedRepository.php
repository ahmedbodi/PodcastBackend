<?php

namespace App\Repository;

use App\Entity\EpisodeDownloaded;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EpisodeDownloaded|null find($id, $lockMode = null, $lockVersion = null)
 * @method EpisodeDownloaded|null findOneBy(array $criteria, array $orderBy = null)
 * @method EpisodeDownloaded[]    findAll()
 * @method EpisodeDownloaded[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeDownloadedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EpisodeDownloaded::class);
    }

    // /**
    //  * @return EpisodeDownloaded[] Returns an array of EpisodeDownloaded objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EpisodeDownloaded
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
