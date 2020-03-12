<?php

namespace App;

use App\Entity\AssessmentModule\Configuration\DocumentGroup;
use App\Entity\AssessmentModule\Configuration\RatingOption;
use App\Entity\AssessmentModule\Metrics\Metric;
use App\Entity\AssessmentModule\Run;
use App\Entity\User;
use Doctrine\ORM\EntityManager;


class DatabaseUtils
{
    /** @var EntityManager em */
    private static $em;


    public static function setEntityManager($em)
    {
        self::$em = $em;
    }


    public static function getDocumentGroups()
    {
        $repo = self::$em->getRepository(DocumentGroup::class);
        $groups = $repo->findBy(array(), ['name' => 'ASC']);

        $document_groups = $document_groups_short = array();
        /** @var DocumentGroup $group */
        foreach ($groups as $group) {
            $document_groups[] = $group->getName();
            $document_groups_short[] = $group->getName();
        }

        return array('document_groups' => $document_groups, 'document_groups_short' => $document_groups_short);
    }


    public static function getActiveUsers()
    {
        $user_repo = self::$em->getRepository(User::class);
        $users = $user_repo->findBy(['isActive' => true], ['username' => 'ASC']);

        $active_user_names_without_admins = $active_user_groups_without_admins
            = $active_user_names = $active_user_groups = array();
        /** @var $user User */
        foreach ($users as $user) {
            if (!in_array(Constants::ROLE_ADMIN, $user->getRoles())) {
                $active_user_names_without_admins[] = $user->getUsername();
                $active_user_groups_without_admins[] = $user->getGroups();
            }
            $active_user_names[] = $user->getUsername();
            $active_user_groups[] = $user->getGroups();
        }

        return array(
            'active_user_names_without_admins' => $active_user_names_without_admins,
            'active_user_groups_without_admins' => $active_user_groups_without_admins,
            'active_user_names' => $active_user_names,
            'active_user_groups' => $active_user_groups
        );
    }


    public static function countUserGroups() {
        $user_groups = self::getActiveUsers()['active_user_groups_without_admins'];

        $group_count = array();
        foreach ($user_groups as $groups) {
            if (!empty($groups)) {
                foreach ($groups as $group) {
                    if (empty($group_count[$group])) {
                        $group_count[$group] = 1;
                    } else {
                        $group_count[$group] += 1;
                    }
                }
            }
        }

        return $group_count;
    }


    public static function getActiveRuns()
    {
        $run_repo = self::$em->getRepository(Run::class);
        $runs = $run_repo->findBy(['active' => true], ['short_name' => 'ASC']);

        $active_run_names = $active_short_run_names = array();
        /** @var $run Run */
        foreach ($runs as $run) {
            $active_run_names[] = $run->getName();
            $active_short_run_names[] = $run->getShortName();
        }

        return array('run_names' => $active_run_names, 'short_names' => $active_short_run_names);
    }


    public static function getRatingOptions()
    {
        $rl_repo = self::$em->getRepository(RatingOption::class);
        return $rl_repo->findBy(array(),['priority' => 'DESC']);
    }

    /**
     * Get metrics from the database that are currently active.
     */
    public static function getActiveMetrics() {
        $metrics_repo = self::$em->getRepository(Metric::class);
        $active_metrics = $metrics_repo->findBy(['active' => true], ['name' => 'ASC', 'k' => 'ASC']);
        return $active_metrics;
    }


    /**
     * Get nr of maximal evaluations for a given document group by
     * querying the database for document group name; if this is not successful, return 0.
     *
     * @param $group
     * @return int
     */
    public static function getNrOfMaxEvaluations($group)
    {
        $return = 0;

        if (strlen($group) > 0) {
            $find = self::$em->getRepository(DocumentGroup::class)
                ->findOneBy(['name' => $group]);
            if (!is_null($find)) {
                $return = $find->getNrOfMaxEvaluations();
            }
        }
        return $return;
    }

}
