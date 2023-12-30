<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
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

//    /**
//     * @return Game[] Returns an array of Game objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Game
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findAllBestGame(): array
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.score', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }
    /**
     * @return Game[] Returns an array of Game objects
     */
    public function findLatestGamesByQuiz($userId): array
        {
            $qb = $this->createQueryBuilder('g');

            $subQuery = $this->_em->createQueryBuilder();
            $subQuery->select('MAX(ga.dateGame)')
                ->from('App\Entity\Game', 'ga')
                ->where('ga.quiz = g.quiz');

            $qb->andWhere('g.dateGame IN (' . $subQuery->getDQL() . ')')
                ->andWhere('g.userId = :userId')
                ->setParameter('userId', $userId);

            return $qb->getQuery()->getResult();
        }
    
   
        public function findBestGameByQuiz($quiz): array
        {
            return $this->createQueryBuilder('g')
                ->where('g.quiz = :quiz')
                ->orderBy('g.score', 'DESC')
                ->setParameter('quiz', $quiz)
                ->setMaxResults(3)
                ->getQuery()
                ->getResult();
        }
    }

// SELECT g.*
// FROM Game g
// JOIN (
//     SELECT MAX(ga.date_game) AS maxDate, ga.quiz_id
//     FROM game ga
//     GROUP BY ga.quiz_id
// ) max_dates
// ON g.date_game = max_dates.maxDate AND g.quiz_id = max_dates.quiz_id;
