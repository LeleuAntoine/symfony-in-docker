<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Game::class);
        $this->em = $em;
    }

    /**
     * @return Game[] Returns a array of the most popular games
     */
    public function findGamesMostPopular(int $maxResult): array
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.download', 'DESC')
            ->andWhere('g.deletedAt IS NULL')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Game[] Returns a array of the last added games
     */
    public function findLastGameAdded(int $maxResult): array
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.id', 'DESC')
            ->andWhere('g.deletedAt IS NULL')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Game[] Returns a array of games not soft deleted
     */
    public function findAllQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.id', 'ASC')
            ->andWhere('g.deletedAt IS NULL')
        ;
    }

    /**
     * @return Game[] Returns a array of games soft deleted
     */
    public function findGamesDeleteAt()
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.deletedAt IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findWithCommentsAndUsers(int $id): ?Game
    {
        return $this->findAllQueryBuilder()
            ->leftJoin('g.comments', 'c')
            ->addSelect('c')
            ->join('c.user', 'u')
            ->addSelect('u')
            ->andWhere('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return Game by id
//     */
//    public function findWithComments(int $gameId): ?Game
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.id = :id')
//            ->leftJoin('g.comments', 'c')
//            ->setParameter('id', $gameId)
//            ->andWhere('c.game =:id')
//            ->andWhere('c.deletedAt IS NULL')
//            ->orderBy('c.modificationDate', 'DESC')
//            ->getQuery()
//            ->getOneOrNullResult();
//
//    }
}
