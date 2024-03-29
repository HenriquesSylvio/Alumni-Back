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
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike, count(distinct comment) as numberComment
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->leftJoin('App:Post', 'comment', JOIN::WITH, 'comment.parentPost = p.id')
            ->where('p.author = ' . $userId)
            ->andWhere('p.mainPost is null')
            ->orderBy('p.createAt', 'desc')
            ->groupBy('p.id, u.id');

        $query = $qb->getQuery();

        return $query->execute();
    }


    public function feed($idUser, $activeUserId, $order = 'desc', $limit = 20, $currentPage = 1)
    {

        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id  and lk2.likeBy = ' . $activeUserId);

        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike, count(distinct comment) as numberComment
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->innerJoin('App:Subscribe', 's', JOIN::WITH, 'u.id = s.subscriber')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->leftJoin('App:Post', 'comment', JOIN::WITH, 'comment.parentPost = p.id')
            ->where('s.subscription= ?1')
            ->andWhere('p.mainPost is null')
            ->orderBy('p.createAt', 'desc')
            ->groupBy('p.id, u.id')
            ->setParameter(1, $idUser);

        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $currentPage);
    }

    public function searchById(string $id, int $activeUserId)
    {
        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id  and lk2.likeBy = ' . $activeUserId);

        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.biography, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike, count(distinct comment) as numberComment
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->leftJoin('App:Post', 'comment', JOIN::WITH, 'comment.parentPost = p.id')
            ->Where('p.id = ?1')
            ->groupBy('p.id, u.id')
            ->setParameter(1, $id);

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function deleteComment(string $id)
    {

        $qb = $this->createQueryBuilder('p')
            ->delete('App:Post', 'p2')
            ->where('p2.mainPost = ?1 or p2.parentPost = ?1')
            ->setParameter(1, $id);;

        $query = $qb->getQuery();
        return $query->execute();
    }
//SELECT distinct p, count(lk) as numberLike, count(c) as test, case  when (select count(*) From public.like_post Where lk.post_id = 974
//					and lk.like_by_id = 371 ) =1 then true else false end as likes
//FROM public.post p
//left join public.like_post lk On lk.post_id = p.id
//left Join public.comment c On c.post_id = p.id
//Where p.id =974
//Group by ( p.*, likes, lk.like_by_id)


    public function getCommentByPost($idPost, $activeUserId, $order = 'desc', $limit = 2, $currentPage = 1)
    {
        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id  and lk2.likeBy = ' . $activeUserId);
//
        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike, count(distinct comment) as numberComment
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->leftJoin('App:Post', 'comment', JOIN::WITH, 'comment.parentPost = p.id')
            ->where('p.parentPost = ' . $idPost)
            ->orderBy('p.createAt', 'desc')
            ->groupBy('p.id, u.id');

        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $currentPage);
    }

    public function search($term, int $activeUserId, $order = 'desc', $limit = 20, $currentPage = 1)
    {
        $subquery  = $this->createQueryBuilder('p2')
            ->select('count(distinct lk2.likeBy)')
            ->from('App:LikePost', 'lk2')
            ->where('lk2.post = p.id  and lk2.likeBy = ' . $activeUserId);

        $qb = $this->createQueryBuilder('p')
            ->select('p.id as idPost, p.content, p.createAt, u.id as idUser, u.firstName, u.lastName, u.biography, u.urlProfilePicture, count(distinct lk.likeBy) as numberLike, count(distinct comment) as numberComment
            , case when (' . $subquery . ') = 1 then true else false end as like')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'p.author = u.id')
            ->leftJoin('App:LikePost', 'lk', JOIN::WITH, 'p.id = lk.post')
            ->leftJoin('App:Post', 'comment', JOIN::WITH, 'comment.parentPost = p.id')
            ->where('p.content LIKE ?1')
            ->andWhere('p.mainPost is null')
            ->orderBy('p.createAt', 'desc')
            ->groupBy('p.id, u.id')
            ->setParameter(1, '%'.$term.'%');
        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $currentPage);
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
