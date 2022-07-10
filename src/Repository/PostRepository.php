<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Parameter;
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

    public function searchByUser(int $userId, int $activeUserId)
    {
        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id and lk2.likeBy = ' . $activeUserId);

        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->where('p.author = ' . $userId)
            ->orderBy('p.createAt', 'desc')
            ->groupBy('p.id, u.id');

        $query = $qb->getQuery();

        return $query->execute();
    }


    public function feed($idUser, $activeUserId, $order = 'desc', $limit = 20, $offset = 0, $currentPage = 1)
    {

        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id  and lk2.likeBy = ' . $activeUserId);

        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->innerJoin('App:Subscribe', 's', JOIN::WITH, 'u.id = s.subscriber')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->where('s.subscription= ?1')
            ->orderBy('p.createAt', $order)
            ->groupBy('p.id, u.id')
            ->setParameter(1, $idUser);

        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $offset, $currentPage);
    }

    public function searchById(string $id, int $activeUserId)
    {
        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id  and lk2.likeBy = ' . $activeUserId);

        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.biography, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->Where('p.id = ?1')
            ->groupBy('p.id, u.id')
            ->setParameter(1, $id);

//        $qb = $this->createQueryBuilder('p')
//            ->select('p,count(lk.likeBy) as numberComment')
//            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
//            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
//            ->leftJoin('App:Comment', 'c', JOIN::WITH, 'p.id = c.post')
//            ->Where('p.id = 94')
//            ->groupBy('p.id')->distinct();

        $query = $qb->getQuery();
//dd($query);
        return $query->execute();
    }
//SELECT distinct p, count(lk) as numberLike, count(c) as test, case  when (select count(*) From public.like_post Where lk.post_id = 974
//					and lk.like_by_id = 371 ) =1 then true else false end as likes
//FROM public.post p
//left join public.like_post lk On lk.post_id = p.id
//left Join public.comment c On c.post_id = p.id
//Where p.id =974
//Group by ( p.*, likes, lk.like_by_id)





    public function search($term, int $activeUserId, $order = 'desc', $limit = 20, $offset = 0, $currentPage = 1)
    {
        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id  and lk2.likeBy = ' . $activeUserId);

        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.biography, u.urlProfilePicture, count(lk.post) as numberLike
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->where('p.content LIKE ?1')
            ->orderBy('p.createAt', $order)
            ->groupBy('p.id, u.id')
            ->setParameter(1, '%'.$term.'%');
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
