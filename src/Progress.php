<?php

namespace App;

use App\Entity\AssessmentModule\DQCombination;
use Doctrine\ORM\EntityManager;


class Progress
{
    /** @var $em EntityManager */
    private static $em;

    public static function setEntityManager($em)
    {
        self::$em = $em;
    }

    /**
     * Calculate progress of currently active user.
     *
     * @param string $user_name
     * @param array $user_groups
     * @return float
     */
    public static function getUserProgress($user_name, $user_groups)
    {
        // Check for setting of needed class variables
        if (!isset(self::$em)) {
            throw new \BadFunctionCallException("Class variable of type EntityManager not set!");
        }

        $total = 0;
        $remain = 0;
        if (is_array($user_groups)) {
            foreach ($user_groups as $group) {
                $total_in_this_group = self::getDQCombinations($group, false, $user_name);
                $remaining_in_this_group = self::getDQCombinations($group, true, $user_name);
                $total += $total_in_this_group;
                $remain += $remaining_in_this_group;
            }
        } else {
            $total = self::getDQCombinations($user_groups, false, $user_name);
            $remain = self::getDQCombinations($user_groups, true, $user_name);
        }

        $assessed = $total - $remain;
        $total > 0
            ? $progress_user = round((($assessed) * 100) / ($total), 1)
            : $progress_user = 0;

        return $progress_user >= 0
            ? $progress_user
            : 0;
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Get total number of DQCombinations in the specified document group.
     *
     * @param $group
     * @param bool $not_assessed
     * @param null $assessor
     * @return array
     */
    private static function getDQCombinations($group, $not_assessed=false, $assessor=null)
    {
        // get no of max evaluations for $group
        DatabaseUtils::setEntityManager(self::$em);
        $nr_evaluations = DatabaseUtils::getNrOfMaxEvaluations($group);

        $repo = self::$em->getRepository(DQCombination::class);
        return $repo->countDQCombinations($group, $not_assessed, $nr_evaluations, $assessor)[0]['nr_sets'];

    }

}
