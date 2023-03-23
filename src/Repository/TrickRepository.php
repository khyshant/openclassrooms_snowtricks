<?php

namespace App\Repository;

use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function add(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllTricks(int $currentPage) : Paginator
    {
        $query = $this->createQueryBuilder('t')
            ->getQuery();
        return $this->paginate($query, $currentPage);
    }


    public function getTricksByAuthor(int $currentPage, User $author, int $limit = 10) : Paginator
    {
        $query = $this->createQueryBuilder("t")
            ->where('t.author = :id')
            ->setParameter('id', $author->getId())
            ->orderBy('t.id','asc');
        return $this->paginate($query, $currentPage, $limit);
    }

    public function paginate($sql,int $page,int $limit = 4) : Paginator
    {
        $paginator = new Paginator($sql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}
