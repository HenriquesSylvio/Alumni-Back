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

//        $qb = $this->createQueryBuilder('u')
//            ->select('u.id, u.firstName, u.lastName, u.biography, u.urlProfilePicture, u.promo, u.acceptAccount, faculty.name as faculty_label, count(follower.subscription) as followerNumber, count(following.subscriber) as followingNumber, case when follower.subscription = ' . $activeUserId . ' then true else false end as subcribe, case when u.id = ' . $activeUserId . ' then true else false end as myProfile')
//            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
//            ->leftJoin('App:Subscribe', 'follower', JOIN::WITH, 'u.id = follower.subscriber')
//            ->leftJoin('App:Subscribe', 'following', JOIN::WITH, 'u.id = following.subscription')
//            ->Where('u.id = ' . $id)
//            ->groupBy('u.id, follower.subscription, faculty.name');
//        //dd($qb->getQuery());
//        $query = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
//        return $query;
//        return $query->execute();

        $qb = $this->createQueryBuilder('u')
            ->select('u.id, u.firstName, u.lastName, u.biography, u.urlProfilePicture, u.promo, u.acceptAccount, faculty.name as faculty_label, count(follower.subscription) as followerNumber, count(following.subscriber) as followingNumber, case when follower2.subscription = ' . $activeUserId . ' then true else false end as subcribe, case when u.id = ' . $activeUserId . ' then true else false end as myProfile')
            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
            ->leftJoin('App:Subscribe', 'follower', JOIN::WITH, 'u.id = follower.subscriber')
            ->leftJoin('App:Subscribe', 'following', JOIN::WITH, 'u.id = following.subscription')
            ->leftJoin('App:Subscribe', 'follower2', JOIN::WITH, 'u.id = follower2.subscriber and ' . $activeUserId . ' = follower2.subscription')
            ->Where('u.id = ' . $id)
//            ->andWhere($activeUserId . ' = follower2.subscription')
            ->groupBy('u.id, faculty.name, follower2.subscription');
        //dd($qb->getQuery());
        $query = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $query;
    }

    public function searchUserWaitingValidation()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id, u.lastName, u.firstName, u.promo, faculty.name as faculty_label')
            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
            ->where('u.acceptAccount = false')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName');

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function search($term, string $activeUserId, $order = 'asc', $limit = 20, $currentPage = 1)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id, u.firstName, u.lastName, u.biography, u.urlProfilePicture, u.promo, faculty.name as faculty_label, case when u.id = ' . $activeUserId . ' then true else false end as myProfile')
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
        $qb->orderBy("u.lastName", $order)
            ->addOrderBy("u.firstName", $order);
        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $currentPage);
    }

    public function searchAdminUser($arrayRole)
    {
//        $roles = ['ROLE_ADMIN'];
//        $qb    = $this->createQueryBuilder('u')
//            ->select('u.id, u.email, u.lastName, u.firstName, u.promo, faculty.name as faculty_label')
//            ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
//            ->where('u.acceptAccount = false')
//            ->orderBy('u.lastName')
//            ->addOrderBy('u.firstName');
//
//        $orStatements = $qb->expr()->orX();
//        foreach ($roles as $role) {
//            $orStatements->add(
//                $qb->expr()
//                    ->like('u.roles', $qb->expr()
//                        ->literal('%"' . $role . '"%'))
//            );
//        }
//
//        $qb->andWhere($orStatements);
//        dd($qb->getQuery()->execute());
//        $users = $qb->getQuery()->getResult();
//
//                $qb = $this->createQueryBuilder('u')
//                    ->select('u.id, u.email, u.lastName, u.firstName, u.promo, faculty.name as faculty_label')
//                    ->innerJoin('App:Faculty', 'faculty', JOIN::WITH, 'u.faculty = faculty.id')
//                    ->where('u.acceptAccount = false')
//                    ->orderBy('u.lastName')
//                    ->addOrderBy('u.firstName');
//
//        $query = $qb->getQuery();
//
//        return $query->execute();

//        $entityManager = $this->getEntityManager();
//
//        $query = $entityManager->createQuery(
//            "SELECT u.roles
//            FROM App\Entity\User u
//            where u.roles = {}"
//        );
//
//        // returns an array of Product objects
//        return $query->getResult();
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'Select id, last_name, first_name From "user" Where roles::text Like :price';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['price' => '%ROLE_ADMIN%']);

        // returns an array of arrays (i.e. a raw data set)
//        dd($stmt->fetchAllAssociative());
        return $resultSet->fetchAllAssociative();

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
