<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Comment[]    findByGame(Game $game)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Comment[] Returns a array of comments by gameId
     */
    public function findComments(int $gameId)
    {
        return $this->createQueryBuilder('c')
            ->setParameter('id', $gameId)
            ->andWhere('c.game =:id')
            ->andWhere('c.deletedAt IS NULL')
            ->orderBy('c.modificationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Comment[] Returns a array of comments soft deleted
     */
    public function findCommentsDeleteAt()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.deletedAt IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}
