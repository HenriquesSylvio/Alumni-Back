<?php

namespace App\Repository;

use App\Entity\Participate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participate>
 *
 * @method Participate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participate[]    findAll()
 * @method Participate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participate::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Participate $entity, bool $flush = true): void
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
    public function remove(Participate $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function searchAllParticipant($idEvent)
    {

        $qb = $this->createQueryBuilder('p')
            ->select('u')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.participant = u.id')
            ->where('p.event = ' . $idEvent);

        $query = $qb->getQuery();

        return $query->execute();

//        $qb = $this->createQueryBuilder('p')
//            ->select('p, count(c) as numberComment, count(lk.post) as numberLike')
//            ->leftJoin('App:Comment', 'c', JOIN::WITH, 'p.id = c.post')
//            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
//            ->where('p.content LIKE ?1')
//            ->orderBy('p.createAt', $order)
//            ->groupBy('p.id')
//            ->setParameter(1, '%'.$term.'%');
//
//        $query = $qb->getQuery();
//
//        return $query->execute();
    }

    // /**
    //  * @return Participate[] Returns an array of Participate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Participate
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
