<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Comment $entity, bool $flush = true): void
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
    public function remove(Comment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function searchById(string $id)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id as idComment, c.content, c.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(rc.answerComment) as numberComment')
            ->leftJoin('App:ReplyComment', 'rc', JOIN::WITH, 'c.id = rc.answerComment')
            ->InnerJoin('App:User', 'u', JOIN::WITH, 'u.id = c.author')
            ->where('c.id = ' . $id)
            ->groupBy('c.id, u.id');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function searchByPost(int $postId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id as idComment, c.content, c.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(rc.answerComment) as numberComment')
            ->leftJoin('App:ReplyComment', 'rc', JOIN::WITH, 'c.id = rc.answerComment')
            ->InnerJoin('App:User', 'u', JOIN::WITH, 'u.id = c.author')
            ->where('c.post = ' . $postId)
            ->groupBy('c.id, u.id');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function searchByComment(int $commentId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id as idComment, c.content, c.createAt, u.id as idUser, u.firstName, u.lastName, u.urlProfilePicture, count(rcR.answerComment) as numberComment')
            ->InnerJoin('App:User', 'u', JOIN::WITH, 'u.id = c.author')
            ->InnerJoin('App:ReplyComment', 'rcA', JOIN::WITH, 'c.id = rcA.replyComment')
            ->LeftJoin('App:ReplyComment', 'rcR', JOIN::WITH, 'c.id = rcR.answerComment')
            ->where('rcA.answerComment = ' . $commentId)
            ->groupBy('c.id, u.id');

        $query = $qb->getQuery();

        return $query->execute();
    }


    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
