<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @return Game[] Returns a 4-row array of the most popular games
     */
    public function findGamesMostPopular(int $value)
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.download', 'DESC')
            ->setMaxResults($value)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Game[] Returns a 4-row array of the last added games
     */
    public function findLastGameAdded($value)
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.id', 'DESC')
            ->setMaxResults($value)
            ->getQuery()
            ->getResult();
    }

//    public function findById($value){
//        return $this->createQueryBuilder('g')
//            ->orderBy('g.id', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }
}
