<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
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

    public function add(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $trick
     * @param $currentPage
     * @return array
     */
    public function getCommentsByTrick (trick $trick, $currentPage): array
    {
        $query = $this->createQueryBuilder("c")
            ->where('c.trick = :id')
            ->setParameter('id', $trick->getId())
            ->orderBy('c.id','DESC');
        return $this->paginate($query, $currentPage)->getQuery()->getResult();
    }

    /**
     * @param $sql
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    public function paginate($sql, int $page, int $limit = 4): Paginator
    {
        $paginator = new Paginator($sql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit);
        return $paginator;
    }
}
