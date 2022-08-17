<?php

namespace App\Repository;

use App\Entity\Faculty;
use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 *
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function add(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchById(string $id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('j.id as idPost, j.title, j.desriptions, j.city, j.company, , j.compensation, j.createAt, u.id as idUser, u.firstName, u.lastName, u.biography, u.urlProfilePicture, f.id as idFaculty, f.name')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'j.author = u.id')
            ->innerJoin('App:Faculty', 'u', JOIN::WITH, 'j.faculty = f.id')
            ->Where('j.id = ?1')
            ->groupBy('j.id, f.id')
            ->setParameter(1, $id);


        $query = $qb->getQuery();

        return $query->execute();
    }

    public function search($term, $order = 'desc', $limit = 20, $offset = 0, $currentPage = 1)
    {

        $qb = $this->createQueryBuilder('p')
            ->select('j.id as idPost, j.title, j.descriptions, j.city, j.company, , j.compensation, j.createAt, u.id as idUser, u.firstName, u.lastName, u.biography, u.urlProfilePicture, f.id as idFaculty, f.name')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'j.author = u.id')
            ->innerJoin('App:Faculty', 'u', JOIN::WITH, 'j.faculty = f.id')
            ->Where('j.title LIKE ?1')
            ->orWhere('j.descriptions LIKE ?1')
            ->orWhere('j.city LIKE ?1')
            ->orWhere('j.company LIKE ?1')
            ->groupBy('j.id, f.id')
            ->setParameter(1, '%'.$term.'%');
        $query = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->paginate($query, $limit, $offset, $currentPage);
    }

    public function searchByUser(int $userId)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('j.id as idPost, j.title, j.descriptions, j.city, j.company, , j.compensation, j.createAt, u.id as idUser, u.firstName, u.lastName, u.biography, u.urlProfilePicture, f.id as idFaculty, f.name')
            ->innerJoin('App:User', 'u', JOIN::WITH, 'j.author = u.id')
            ->innerJoin('App:Faculty', 'u', JOIN::WITH, 'j.faculty = f.id')
            ->Where('p.author = ' . $userId)
            ->groupBy('j.id, u.id');

        $query = $qb->getQuery();

        return $query->execute();
    }

}
