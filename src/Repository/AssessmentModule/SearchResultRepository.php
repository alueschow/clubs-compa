<?php

namespace App\Repository\AssessmentModule;

use App\Entity\AssessmentModule\Assessment;
use App\Entity\AssessmentModule\Document;
use App\Entity\AssessmentModule\DQCombination;
use App\Entity\AssessmentModule\SearchResult;
use Doctrine\ORM\EntityManagerInterface;


class SearchResultRepository extends BaseAssessmentRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);
        $this->em = $em;
    }

    public function findAll() {
        return parent::findAllBase(SearchResult::class);
    }

    /**
     * Get number of documents that were found for a query.
     * Number of documents can be different in each run.
     *
     * @param $query
     * @param $run_id
     * @return array
     */
    public function findTotalNumFoundForQueryInRun($query, $run_id) {
        return $this->em->createQueryBuilder()
            ->select("s.num_found")
            ->from(SearchResult::class, 's')
            ->where("s.query = '" . $query . "'")
            ->andWhere("s.run_id = '" . $run_id . "'")
            ->distinct()
            ->getQuery()
            ->getResult();
    }


    /**
     * Query for documents up to rank r for a given query, that
     * (a) were already assessed, and
     * (b) that are either highly or partially relevant.
     *
     * @param $query
     * @param $run
     * @param int $limit Cuts off the result list at a given rank
     * @return array
     */
    public function findRelevantDocsForQueryInRun($query, $run, $limit = -1) {

        $qb = $this->em->createQueryBuilder();
        $res = $this->em->createQueryBuilder()
            ->select("count(DISTINCT s.document) as docs")
            ->from(SearchResult::class, 's')
            ->leftJoin(Assessment::class, "a", "WITH",
                $qb->expr()->andX(
                    $qb->expr()->eq("s.document", "a.document"),
                    $qb->expr()->eq('s.query', 'a.query')
                )
            )
            ->where("s.query = '" . $query . "'")
            ->andWhere("s.run_id = '" . $run . "'")
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq("a.rating", "'Highly relevant'"),
                    $qb->expr()->eq("a.rating", "'Partially relevant'")
                )
            );

        if ($limit > -1) {
            $res = $res->andWhere("s.rank <= '" . $limit . "'");
        }

        return $res->getQuery()->getResult();
    }


    /**
     * Count total number of SearchResults with a given run ID.
     *
     * @param $runid
     * @return array
     */
    public function countSearchResultsForRun($runid)
    {
        return $this->em->createQueryBuilder()
            ->select("count(s.document) as documents")
            ->from(SearchResult::class, 's')
            ->where("s.run_id = '" . $runid . "'")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find documents in base language for a run ID.
     *
     * @param $runid
     * @param $base_lang
     * @return array
     */
    public function findBaseDocsForRun($runid, $base_lang)
        // TODO: seems to be unused
    {
        return $this->em->createQueryBuilder()
            ->select("count(s.document) as documents")
            ->from(SearchResult::class, 's')
            ->leftJoin(Document::class, "d", "WITH", "d.doc_id=s.document")
            ->where("s.run_id = '" . $runid . "'")
            ->andWhere("d.lang = '" . $base_lang . "'")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find all ranks for a given DQCombination ID
     *
     * @param $id
     * @return array
     */
    public function findRanks($id)
    {
        $qb = $this->em->createQueryBuilder();

        return $this->em->createQueryBuilder()
            ->select("s.rank as ranks")
            ->from(SearchResult::class, 's')
            ->leftJoin(DQCombination::class, "dq", "WITH",
                $qb->expr()->andX(
                    $qb->expr()->eq("dq.document", "s.document"),
                    $qb->expr()->eq('dq.query', 's.query')
                ))
            ->where("dq.id = " . $id)
            ->getQuery()
            ->getResult();
    }


    /**
     * Find all run IDs for a given DQCombination ID
     *
     * @param $id
     * @return array
     */
    public function findRunIds($id)
    {
        $qb = $this->em->createQueryBuilder();

        return $this->em->createQueryBuilder()
            ->select("s.run_id as run_ids")
            ->from(SearchResult::class, 's')
            ->leftJoin(DQCombination::class, "dq", "WITH",
                $qb->expr()->andX(
                    $qb->expr()->eq("dq.document", "s.document"),
                    $qb->expr()->eq('dq.query', 's.query')
                ))
            ->where("dq.id = " . $id)
            ->getQuery()
            ->getResult();
    }


    /**
     * Find all num_found for a given DQCombination ID
     *
     * @param $id
     * @return array
     */
    public function findNumFound($id)
    {
        $qb = $this->em->createQueryBuilder();

        return $this->em->createQueryBuilder()
            ->select("s.num_found as num_founds")
            ->from(SearchResult::class, 's')
            ->leftJoin(DQCombination::class, "dq", "WITH",
                $qb->expr()->andX(
                    $qb->expr()->eq("dq.document", "s.document"),
                    $qb->expr()->eq('dq.query', 's.query')
                ))
            ->where("dq.id = " . $id)
            ->getQuery()
            ->getResult();
    }

}