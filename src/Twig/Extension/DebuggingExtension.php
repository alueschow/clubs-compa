<?php

namespace App\Twig\Extension;

use App\Entity\AssessmentModule\Configuration\MainConfiguration;
use App\Entity\AssessmentModule\Configuration\GeneralConfiguration;
use App\Entity\ComparisonModule\Configuration\LiveConfiguration;
use App\Entity\ComparisonModule\Configuration\StandaloneConfiguration;
use Doctrine\ORM\EntityManager;


class DebuggingExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGlobals()
    {
        /**
         * @var $repo MainConfiguration
         */
        $repo = $this->em->getRepository(MainConfiguration::class)->find(1);
        $repo_2 = $this->em->getRepository(GeneralConfiguration::class)->find(1);
        $repo_3 = $this->em->getRepository(\App\Entity\ComparisonModule\Configuration\MainConfiguration::class)->find(1);
        $repo_4 = $this->em->getRepository(StandaloneConfiguration::class)->find(1);
        $repo_5 = $this->em->getRepository(LiveConfiguration::class)->find(1);

        if ($repo_2->getGroupBy()) {
            $assessment_grouped = true;
            $assessment_groupedBy = $repo_2->getGroupByCategory();
        } else {
            $assessment_grouped = false;
            $assessment_groupedBy = null;
        }

        if ($repo_4->getGroupBy()) {
            $comparison_grouped = true;
            $comparison_groupedBy = $repo_4->getGroupByCategory();
        } else {
            $comparison_grouped = false;
            $comparison_groupedBy = null;
        }

        return [
            'currently_active_assessment_mode' => $repo->getMode(),
            'currently_active_comparison_mode' => $repo_3->getMode(),
            'currently_active_assessment_category' => $repo->getActiveStudyCategory(),
            'currently_active_comparison_category' => $repo_3->getActiveStudyCategory(),
            'use_metrics' => $repo->getUseMetricsStatus(),
            'assessment_debug_mode_on' => $repo->getDebugModeStatus(),
            'comparison_debug_mode_on' => $repo_3->getDebugModeStatus(),
            'assessment_grouped' => $assessment_grouped,
            'assessment_grouped_by' => $assessment_groupedBy,
            'comparison_grouped' => $comparison_grouped,
            'comparison_grouped_by' => $comparison_groupedBy,
            'loadingNewDocument' => $repo_2->getLoadingNewDocument(),
            'comparison_randomized_standalone' => $repo_4->getRandomization() ? 'yes' : 'no',
            'comparison_randomized_live' => $repo_5->getRandomization() ? 'yes' : 'no',
        ];
    }
}