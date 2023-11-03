<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

//    /**
//     * @return Question[] Returns an array of Question objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Question
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function questionsNotInQuiz(int $quizId)
    {
        $em = $this->getEntityManager();

        // Créer la sous-requête pour obtenir les question appartenant au quiz spécifique
        $subquery = $em->createQueryBuilder();
        $subquery->select('q.id')
        ->from('App\Entity\Question', 'q')
        ->leftJoin('q.quiz', 'qu')
        ->where('qu.id = :id');

        // Créer la requête principale pour obtenir les questions non utilisé
        $qb = $em->createQueryBuilder();
        $qb->select('que')
        ->from('App\Entity\Question', 'que')
        ->where($qb->expr()->notIn('que.id', $subquery->getDQL()))
        ->setParameter('id', $quizId)
        ->orderBy('que.sentence');

    $query = $qb->getQuery();

    return $query->getResult();
    }
}
