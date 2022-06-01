<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function add(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function conversations(int $userId)
    {
        $subquery  = $this->createQueryBuilder('m2')
            ->select('Max(m2.id)')
            ->where('m2.sentBy = ?1 Or m2.receivedBy = ?1')
            ->setParameter(1, $userId)
            ->getDQL();
        $qb = $this->createQueryBuilder('m');
        $qb->select('Distinct u.id, u.firstName, u.lastName, m.content as lastMessage, m.createAt')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'm.sentBy = u.id Or m.receivedBy = u.id')
            ->where('not u.id = ?1')
            ->andWhere('(m.sentBy = ?1 Or m.receivedBy = ?1)')
            ->andWhere($qb->expr()->in('m.id',$subquery))
            ->setParameter(1, $userId);


        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
//        return  $qb->getQuery()->getOneOrNullResult();
    }
    public function messages(int $activeUser, int $idOtherUser)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('u.id, u.firstName, u.lastName, m.id, m.content, m.createAt')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'm.sentBy = u.id')
            ->Where('m.sentBy = ?1 And m.receivedBy = ?2')
            ->orWhere('m.receivedBy = ?1 And m.sentBy = ?2')
            ->orderBy('m.id', 'DESC')
            ->setParameter(1, $activeUser)
            ->setParameter(2, $idOtherUser);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
//        return  $qb->getQuery()->getOneOrNullResult();
    }


//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
