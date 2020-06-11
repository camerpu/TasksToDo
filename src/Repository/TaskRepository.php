<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findEnded()
    {
        return $this->createQueryBuilder("a")
            ->where("a.status = :ended")
            ->setParameter("ended", Task::ST_FINISHED)
            ->orderBy("a.updatedAt", "DESC")
            ->getQuery()
            ->getResult();
    }

    public function findActive()
    {
        return $this->createQueryBuilder("a")
            ->where("a.status = :st")
            ->setParameter("st", Task::ST_TOOK)
            ->orWhere("a.status = :st2")
            ->setParameter('st2', Task::ST_WAITING)
            ->orderBy("a.updatedAt", "DESC")
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
