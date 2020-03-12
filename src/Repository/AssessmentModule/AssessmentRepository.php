<?php

namespace App\Repository\AssessmentModule;

use App\Entity\AssessmentModule\Assessment;
use App\Entity\AssessmentModule\SearchResult;
use Doctrine\ORM\EntityManagerInterface;


class AssessmentRepository extends BaseAssessmentRepository
{
    private $em;
    
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);
        $this->em = $em;
    }
    
    public function findAll() {
        return parent::findAllBase(Assessment::class);
    }

    /**
     * Find total number of assessments for a given rating.
     *
     * @param $short_name
     * @param $level_name
     * @return array
     */
    public function findRatingCount($short_name, $level_name)
    {
        return $this->em->createQueryBuilder()
            ->select("count(a.rating) as col_" . $short_name)
            ->from(Assessment::class, 'a')
            ->where("a.rating = '" . $level_name . "'")
            ->getQuery()
            ->getResult();
    }


    /**
     * Count number of assessments for a run with a given ID.
     *
     * @param $runid
     * @return array
     */
    public function countAssessmentsForRun($runid)
    {
        $qb = $this->em->createQueryBuilder();

        return $this->em->createQueryBuilder()
            ->select("count(a.document) as documents")
            ->from(Assessment::class, 'a')
            ->leftJoin(SearchResult::class, "s", "WITH",
                $qb->expr()->andX(
                    $qb->expr()->eq("a.document", "s.document"),
                    $qb->expr()->eq('a.query', 's.query')
                ))
            ->where("s.run_id = '" . $runid . "'")
            ->getQuery()
            ->getResult();
    }


    /**
     * Count number of documents in a run with a given rating.
     *
     * @param $runid
     * @param $rating
     * @return array
     */
    public function countRatingsForRun($runid, $rating)
    {
        $qb = $this->em->createQueryBuilder();

        return $this->em->createQueryBuilder()
            ->select("count(a.document) as documents")
            ->from(Assessment::class, 'a')
            ->leftJoin(SearchResult::class, "s", "WITH",
                $qb->expr()->andX(
                    $qb->expr()->eq("a.document", "s.document"),
                    $qb->expr()->eq('a.query', 's.query')
                ))
            ->where("s.run_id = '" . $runid . "'")
            ->andWhere("a.rating = '" . $rating . "'")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find documents that are highly relevant for a query.
     *
     * @param $query
     * @param $level_name
     * @return array
     */
    public function findRelevant($query, $level_name)
    {
        return $this->em->createQueryBuilder()
            ->select("DISTINCT IDENTITY(a.document) as document")
            ->from(Assessment::class, 'a')
            ->where("a.query = '" . $query . "'")
            ->andWhere("a.rating = '" . $level_name . "'")
            ->getQuery()
            ->getResult();
    }

}