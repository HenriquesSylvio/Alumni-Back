<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function searchById(string $id, string $activeUserId){

        $qb = $this->createQueryBuilder('u')
            ->select('u.id, u.firstName, u.lastName, u.biography, u.urlProfilePicture, u.promo, u.acceptAccount, faculty.name as faculty_label, count(follower.subscription) as followerNumber, count(following.subscriber) as followingNumber, case when follower.subscription = ' . $activeUserId . ' then true else false end as subcribe')
            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
            ->leftJoin('App:Subscribe', 'follower', JOIN::WITH, 'u.id = follower.subscriber')
            ->leftJoin('App:Subscribe', 'following', JOIN::WITH, 'u.id = following.subscription')
            ->Where('u.id = ' . $id)
            ->groupBy('u.id, follower.subscription, faculty.name');
        //dd($qb->getQuery());
        $query = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $query;
        return $query->execute();
    }

    public function searchUserWaitingValidation()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id, u.email, u.lastName, u.firstName, u.promo, faculty.name as faculty_label')
            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
            ->where('u.acceptAccount = false')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function search($term, $order = 'asc', $limit = 20, $offset = 0, $currentPage = 1)
    {
        $qb = $this->createQueryBuilder('u')
            ->select("u.id, u.firstName, u.lastName, u.biography, u.urlProfilePicture, u.promo, faculty.name as faculty_label")
            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id');
        if (!is_null($term)){
            $qb->where("DIFFERENCE(u.firstName, ?1) = 4")
                ->orWhere("DIFFERENCE(u.lastName, ?1) = 4")
                ->orderBy("DIFFERENCE(u.lastName, ?1)")
                ->setParameter(1, $term);
            if(count($qb->getQuery()->getResult()) == 0){
                $qb->where("DIFFERENCE(u.firstName, ?1) = 3")
                    ->orWhere("DIFFERENCE(u.lastName, ?1) = 3")
                    ->orderBy("DIFFERENCE(u.lastName, ?1)");
            }
            $qb->setParameter(1, $term);
        }
        $qb->andwhere("u.acceptAccount = true");

        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $offset, $currentPage);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
