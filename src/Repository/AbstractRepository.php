<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Pagerfanta\Adapter\NullAdapter;
use Pagerfanta\Adapter\QueryAdapter;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Adapter\ConcatenationAdapter;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

//use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends EntityRepository
{
    protected function paginate(array $qb, $limit = 20, $offset = 0, $currentPage = 1)
    {
        if (0 == $limit) {
            throw new \LogicException('$limit & $offstet must be greater than 0.');
        }
        $adapter = new ArrayAdapter($qb);
        $pager = new Pagerfanta($adapter);
        //$currentPage = ceil(($offset + 1)/ $limit);
        $pager->setCurrentPage($currentPage);
        $pager->setMaxPerPage((int) $limit);
        //dd($pager->getNbPages());
        return $pager;
    }
}