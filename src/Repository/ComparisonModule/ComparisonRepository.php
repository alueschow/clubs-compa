<?php

namespace App\Repository\ComparisonModule;

use App\Entity\ComparisonModule\Comparison;
use Doctrine\ORM\EntityManagerInterface;


class ComparisonRepository extends BaseComparisonRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);
        $this->em = $em;
    }

    public function findAll() {
        return parent::findAllBase(Comparison::class);
    }


    /**
     * Find all combinations that were already evaluated side-by-side for a given user.
     * Return preferred and other document of these evaluations.
     *
     * @param String $username
     * @return array
     */
    public function findCombinationsForUser($username)
    {
        return $this->em->createQueryBuilder()
            ->select("pe.preferred_document, pe.other_document")
            ->from(Comparison::class, "pe")
            ->where("pe.tester = '" . $username . "'")
            ->getQuery()
            ->getResult();
    }


    /**
     * Find total number of evaluations.
     *
     * @param $live
     * @return array
     */
    public function findTotalEvaluations($live=false)
    {
        if ($live) {
            return $this->em->createQueryBuilder()
                ->select("count(e.preferred_document) as pref")
                ->from(Comparison::class, 'e')
                ->where("e.tester LIKE '%.%'")
                ->getQuery()
                ->getResult();
        } else {
            return $this->em->createQueryBuilder()
                ->select("count(e.preferred_document) as pref")
                ->from(Comparison::class, 'e')
                ->where("e.tester NOT LIKE '%.%'")
                ->getQuery()
                ->getResult();
        }
    }


    /**
     * Find number of times that $website was preferred.
     *
     * @param $document
     * @param $live
     * @return array
     */
    public function findDocumentPreferences($document, $live=false)
    {
        if ($live) {
            return $this->em->createQueryBuilder()
                ->select("count(e.preferred_document) as pref")
                ->from(Comparison::class, 'e')
                ->where("e.preferred_document = '" . $document . "'")
                // include IP addresses
                ->andWhere("e.tester LIKE '%.%'")
                ->getQuery()
                ->getResult();
        } else {
            return $this->em->createQueryBuilder()
                ->select("count(e.preferred_document) as pref")
                ->from(Comparison::class, 'e')
                ->where("e.preferred_document = '" . $document . "'")
                // exclude IP addresses
                ->andWhere("e.tester NOT LIKE '%.%'")
                ->getQuery()
                ->getResult();
        }
    }


    /**
     * Find number of times that $document was NOT preferred.
     *
     * @param $document
     * @param $live
     * @return array
     */
    public function findDocumentNonPreferences($document, $live = false)
    {
        if ($live) {
            return $this->em->createQueryBuilder()
                ->select("count(e.other_document) as other")
                ->from(Comparison::class, 'e')
                ->where("e.other_document = '" . $document . "'")
                ->andWhere("e.tester LIKE '%.%'")
                ->getQuery()
                ->getResult();
        } else {
            return $this->em->createQueryBuilder()
                ->select("count(e.other_document) as other")
                ->from(Comparison::class, 'e')
                ->where("e.other_document = '" . $document . "'")
                ->andWhere("e.tester NOT LIKE '%.%'")
                ->getQuery()
                ->getResult();
        }
    }

}