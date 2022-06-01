<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Post $entity, bool $flush = true): void
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
    public function remove(Post $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function searchByUser(int $userId)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, count(c) as numberComment, count(lk.post) as numberLike')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:Comment', 'c', JOIN::WITH, 'p.id = c.post')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->where('p.author = ' . $userId)
            ->orderBy('p.createAt', 'desc')
            ->groupBy('p.id, u.id');

        $query = $qb->getQuery();

        return $query->execute();
    }
    public function searchById(string $id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, count(c) as numberComment, count(lk.post) as numberLike')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:Comment', 'c', JOIN::WITH, 'p.id = c.post')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->Where('p.id = ?1')
            ->groupBy('p.id, u.id')
            ->setParameter(1, $id);

        $query = $qb->getQuery();

        return $query->execute();
    }


    public function search($term, $order = 'desc', $limit = 20, $offset = 0, $currentPage = 1)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, count(c) as numberComment, count(lk.post) as numberLike')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:Comment', 'c', JOIN::WITH, 'p.id = c.post')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->where('p.content LIKE ?1')
            ->orderBy('p.createAt', $order)
            ->groupBy('p.id, u.id')
            ->setParameter(1, '%'.$term.'%');
        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $offset, $currentPage);
    }

    public function feed($idUser, $order = 'desc', $limit = 20, $offset = 0, $currentPage = 1)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, count(c) as numberComment, count(lk.post) as numberLike')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->innerJoin('App:Subscribe', 's', JOIN::WITH, 'u.id = s.subscriber')
            ->leftJoin('App:Comment', 'c', JOIN::WITH, 'p.id = c.post')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->where('s.subscription= ?1')
            ->orderBy('p.createAt', $order)
            ->groupBy('p.id, u.id')
            ->setParameter(1, $idUser);

        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $offset, $currentPage);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
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
    public function findOneBySomeField($value): ?Post
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
