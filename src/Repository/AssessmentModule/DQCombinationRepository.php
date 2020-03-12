<?php

namespace App\Repository\AssessmentModule;

use App\Entity\AssessmentModule\Assessment;
use App\Entity\AssessmentModule\Document;
use App\Entity\AssessmentModule\DQCombination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;


class DQCombinationRepository extends BaseAssessmentRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);
        $this->em = $em;
    }

    public function findAll() {
        return parent::findAllBase(DQCombination::class);
    }

    public function find($id) {
        return parent::findBase(DQCombination::class, 'id', $id);
    }


    /**
     * Find total number of skips.
     *
     * @param $name
     * @return array
     */
    public function findSkips($name)
    {
        return $this->em->createQueryBuilder()
            ->select("count(dq.skipped) as col_" . $name)
            ->from(DQCombination::class, 'dq')
            ->where("dq.skipped = 1")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find total number of assessments.
     *
     * @return array
     */
    public function findTotalAssessments()
    {
        return $this->em->createQueryBuilder()
            ->select("count(dq.document) as total_assessments")
            ->from(DQCombination::class, 'dq')
            ->where("dq.skipped = 0")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find number of finished assessments.
     *
     * @return array
     */
    public function findFinishedAssessments()
    {
        return $this->em->createQueryBuilder()
            ->select("count(dq.document) as finished_assessments")
            ->from(DQCombination::class, 'dq')
            ->where("dq.evaluated > 0")
            ->andWhere("dq.skipped = 0")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find total number of entries with specified group.
     *
     * @param $group
     * @return array
     */
    public function findTotalNumberForDocGroup($group)
    {
        return $this->em->createQueryBuilder()
            ->select("count(dq.document) as " .  $group . "_assessments")
            ->from(DQCombination::class, 'dq')
            ->leftJoin(Document::class, "d", "WITH", "dq.document = d.doc_id")// Join with the Documents table
            ->where("d.doc_group = '" . $group . "'")
            ->andWhere('dq.skipped = 0')
            ->getQuery()
            ->getResult();
    }


    /**
     * Find number of evaluations for an ID.
     *
     * @param $id
     * @return array
     */
    public function findNrOfEvaluations($id)
    {
        return $this->em->createQueryBuilder()
            ->select("dq.evaluated")
            ->from(DQCombination::class, 'dq')
            ->where("dq.id ='" . $id . "'")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find number of entries with specified document group that were already assessed.
     *
     * @param $group
     * @param int $how_often
     * @return array
     */
    public function findDocGroup($group, $how_often=1)
    {
        return $this->em->createQueryBuilder()
            ->select("count(dq.document) as " . $group . "_assessments")
            ->from(DQCombination::class, 'dq')
            ->innerJoin(Document::class, "d", "WITH", "dq.document = d.doc_id")
            ->where("d.doc_group = '" . $group . "'")
            ->andWhere("dq.evaluated >= " . $how_often)
            ->andWhere('dq.skipped = 0')
            ->getQuery()
            ->getResult();
    }


    /**
     * Find DQCombinations that
     * (a) have a document in the specified document group,
     * (b) were not rated $nr_evaluations times,
     * (c) were not rated by the specified assessor yet,
     * (d) (optional) were postponed.
     *
     * @param $group
     * @param $assessor
     * @param $nr_evaluations
     * @param bool $postponed
     * @return array
     */
    public function findUnratedDQCombinations($group, $assessor, $nr_evaluations, $postponed=false)
    {
            $qb = $this->em->createQueryBuilder();

            // This subquery makes sure that no document/query combinations are rated twice by the same assessor
            // by selecting all DQCombinations that were already assessed by the assessor
            $sub = $qb->select('1')
                ->from(DQCombination::class, "dq2")
                ->leftJoin(Assessment::class, "a2", "WITH",
                    $qb->expr()->andX(
                        $qb->expr()->eq("dq2.document", "a2.document"),
                        $qb->expr()->eq('dq2.query', 'a2.query')
                    ))
                ->where("a2.assessor = '" . $assessor . "'")
                // Primary Key of DQCombination table
                ->andWhere('dq2.document = dq.document')
                ->andWhere('dq2.query = dq.query');

            // This query selects all document/query combinations ...
            $query = $this->em->createQueryBuilder()
                ->select("dq")
                ->from(DQCombination::class, 'dq')
                ->leftJoin(Document::class, "d", "WITH", "dq.document = d.doc_id")
                ->leftJoin(Assessment::class, "a", "WITH",
                    $qb->expr()->andX(
                        $qb->expr()->eq("dq.document", "a.document"),
                        $qb->expr()->eq('dq.query', 'a.query')
                    ))
                // ... with the appropriate document group ...
                ->where("d.doc_group = '" . $group . "'")
                // ... where the assessor column is not the current assessor ...
                ->andWhere("a.assessor <> '" . $assessor . "' OR a.assessor is null")
                // ... up to $nr_base_evaluations evaluations allowed ...
                ->andWhere("dq.evaluated < " . $nr_evaluations)
                ->andWhere("dq.skipped = 0");

            $postponed
                ? $query = $query->andWhere("dq.postponed = 1")
                : $query = $query->andWhere("dq.postponed = 0");

            // ... and document/query combination not already assessed by the assessor, using the subquery above
            $query = $query->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));

            return $query->getQuery()->getResult();

    }


    /**
     * Count all DQCombinations for a document group (that were not skipped).
     * If parameter $not_assessed is true, only those DQCombinations which were not yet assessed are count.
     *
     * @param $group
     * @param $not_assessed
     * @param null $nr_evaluations
     * @param null $assessor
     * @return array
     */
    public function countDQCombinations($group, $not_assessed, $nr_evaluations=null, $assessor=null)
    {
        $qb = $this->em->createQueryBuilder();

        if ($not_assessed) {
            if (is_null($nr_evaluations) || is_null($assessor)) {
                throw new InvalidParameterException("Parameters nr_evaluations and assessor must be set!");
            } else {
                // Get number of documents that are not yet complete evaluated and that the
                // assessor has not evaluated
                $query = $this->em->createQueryBuilder()
                    ->select("count(DISTINCT dq) as nr_sets")
                    ->from(DQCombination::class, 'dq')
                    ->leftJoin(Document::class, "d", "WITH", "dq.document = d.doc_id")
                    ->leftJoin(Assessment::class, "a", "WITH",
                        $qb->expr()->andX(
                            $qb->expr()->eq("dq.document", "a.document"),
                            $qb->expr()->eq('dq.query', 'a.query')
                        ))
                    ->where("a.assessor <> '" . $assessor . "' OR a.assessor is null")
                    ->andWhere("d.doc_group = '" . $group . "'")
                    ->andWhere("dq.evaluated < " . $nr_evaluations)
                    ->andWhere("dq.skipped = 0");
            }
        } else {
            $query = $this->em->createQueryBuilder()
                ->select("count(dq) as nr_sets")
                ->from(DQCombination::class, 'dq')
                ->leftJoin(Document::class, "d", "WITH", "dq.document = d.doc_id")
                ->where("d.doc_group = '" . $group . "'")
                ->andWhere("dq.skipped = 0");
        }

        return $query->getQuery()->getResult();

    }

}