<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Event $entity, bool $flush = true): void
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
    public function remove(Event $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function search($activeUserId, $term = "", $order = 'asc', $past = false, $limit = 20, $offset = 0, $currentPage = 1)
    {

        $subquery  = $this->createQueryBuilder('e2')
            ->select('count(distinct p.participant)')
            ->from('App:Participate', 'p')
            ->where('p.event = e.id  and p.participant = ' . $activeUserId);

        $qb = $this->createQueryBuilder('e')
            ->select('e.id as idEvent, e.title, e.description, e.date, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, case when (' . $subquery . ') = 1 then true else false end as participate')
            ->InnerJoin('App:User', 'u', JOIN::WITH, 'u.id = e.author')
            ->where('e.title LIKE ?1')
            ->orWhere('e.description LIKE ?1')
            ->setParameter(1, '%' . $term . '%');
        if ($past){
            $qb->andWhere('e.date < :date')
            ->setParameter('date', date("Y-m-d"));
        }else{
            $qb->andWhere('e.date >= :date')
                ->setParameter('date', date("Y-m-d"));
        }

        $qb->orderBy('e.date', $order);

//        dd($qb->getQuery());
        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $offset, $currentPage);
    }

    public function allDate()
    {
        $qb = $this->createQueryBuilder('e')
            ->select('distinct e.date')
            ->orderBy('e.date', 'asc');

        $query = $qb->getQuery();

        return $query->execute();
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
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
    public function findOneBySomeField($value): ?Event
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
