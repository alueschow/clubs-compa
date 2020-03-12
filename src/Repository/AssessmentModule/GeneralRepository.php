<?php

namespace App\Repository\AssessmentModule;

use App\Constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;


/**
 * Class GeneralRepository is a service which comprises functions
 * that are either rather general or that need more than one Repository.
 *
 * @package App\Repository
 */
class GeneralRepository
{
    private $em;
    private $dq_repo;
    private $as_repo;
    private $sr_repo;


    public function __construct(EntityManagerInterface $em, DQCombinationRepository $dq_repo, AssessmentRepository $as_repo,
                                SearchResultRepository $sr_repo)
    {
        $this->em = $em;
        $this->dq_repo = $dq_repo;
        $this->as_repo = $as_repo;
        $this->sr_repo = $sr_repo;
    }

    /**
     * Query database for entries with specified relevance level.
     *
     * @param $level_name
     * @param $short_level_name
     * @return array
     */
    public function assessmentByRating($level_name, $short_level_name=null)
    {
        if ($level_name != Constants::SKIPPED) {
            return $this->as_repo->findRatingCount($short_level_name, $level_name);
        } else {
            return $this->dq_repo->findSkips($level_name);
        }
    }

    /**
     * Query database for entries with a specified document group.
     *
     * @param $group
     * @param bool $total
     * @param int $how_often
     * @return array
     */
    public function assessmentByDocGroup($group, $total=false, $how_often=1)
    {
        if ($total) {
            return $this->dq_repo->findTotalNumberForDocGroup($group);
        } else {
            return $this->dq_repo->findDocGroup($group, $how_often);
        }
    }


    /**
     * Query database for entries with specified run ID.
     *
     * @param $type
     * @param $runid
     * @param $rating
     * @return array
     */
    public function assessmentByRun($type, $runid, $rating=null)
    {
        switch($type) {
            case "rating_levels":
                return $this->as_repo->countRatingsForRun($runid, $rating)[0]['documents'];
            case "assessed":
                return $this->as_repo->countAssessmentsForRun($runid)[0]['documents'];
            case "total":
                return $this->sr_repo->countSearchResultsForRun($runid)[0]['documents'];
            default:
                throw new InvalidParameterException('Wrong type parameter!');
        }
    }

}