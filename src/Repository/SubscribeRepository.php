<?php

namespace App\Repository;

use App\Entity\Subscribe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscribe>
 *
 * @method Subscribe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscribe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscribe[]    findAll()
 * @method Subscribe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscribeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscribe::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Subscribe $entity, bool $flush = true): void
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
    public function remove(Subscribe $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function searchAllSubscriber($idUser)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('u.id, u.firstName, u.lastName, u.urlProfilePicture, u.promo, faculty.name as faculty_label')
            ->innerJoin('App:User', 'u', JOIN::WITH, 's.subscription = u.id')
            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
            ->where('s.subscriber = ' . $idUser);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function searchAllSubscription($idUser)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('u.id, u.firstName, u.lastName, u.urlProfilePicture, u.promo, faculty.name as faculty_label')
            ->innerJoin('App:User', 'u', JOIN::WITH, 's.subscriber = u.id')
            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
            ->where('s.subscription = ' . $idUser);

        $query = $qb->getQuery();

        return $query->execute();
    }

    // /**
    //  * @return Subscribe[] Returns an array of Subscribe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subscribe
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
