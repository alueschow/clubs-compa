<?php

namespace App;

use App\Services\AssessmentStatistics;
use App\Services\Metrics;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Exception\InvalidParameterException;


class ResultsUtils
{
    /** @var EntityManager em */
    private static $em;
    private static $active_study_category;
    private static $metrics_config;


    public static function setEntityManager($em)
    {
        self::$em = $em;
    }

    public static function setActiveStudyCategory($a)
    {
        self::$active_study_category = $a;
    }

    public static function setMetricsConfiguration($m) {
        self::$metrics_config = $m;
    }


    /**
     * Get basic statistics.
     *
     * @param AssessmentStatistics $statistics
     * @return array
     */
    public static function getBasicResults(AssessmentStatistics $statistics) {
        return $statistics->getBasicStatistics();
    }


    /**
     * Collect needed information from database and get detailed statistics.
     *
     * @param AssessmentStatistics $statistics
     * @param Metrics Service $metrics
     * @return array
     */
    public static function getDetailedResults(AssessmentStatistics $statistics, Metrics $metrics)
    {
        DatabaseUtils::setEntityManager(self::$em);
        /* Get currently active metrics from the database */
        $active_metrics = DatabaseUtils::getActiveMetrics();
        /* Get rating options that are relevant */
        $relevance_levels = DatabaseUtils::getRatingOptions();
        /* Get run names from database */
        $active_run_names = DatabaseUtils::getActiveRuns()['run_names'];
        $active_short_run_names = DatabaseUtils::getActiveRuns()['short_names'];
        /* Initialize Metrics service and call method for more detailed statistics */
        $metrics::init(self::$metrics_config);
        $stats = $statistics->getDetailedAssessmentStatistics($active_metrics, $relevance_levels, $active_run_names);

        return self::createDataArray(Constants::ASSESSMENT_MODULE_DETAILED_RESULTS, $stats, $active_metrics, $active_short_run_names);
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Create an array with the data that will be passed to the template.
     *
     * @param $type
     * @param $stats
     * @param $active_metrics
     * @param null $short_run_names
     * @return array
     */
    private static function createDataArray($type, $stats, $active_metrics=null, $short_run_names=null) {
        if ($type == Constants::ASSESSMENT_MODULE_BASIC_RESULTS) {
            return
                array(
                    'total_assessments' => $stats['total_assessments'],
                    'finished_assessments' => $stats['finished_assessments'],
                    'assessment_prop' => $stats['assessment_prop'],
                    'doc_groups_assessments' => $stats['doc_groups_assessments'],
                    'doc_groups_assessments_total' => $stats['doc_groups_assessments_total'],
                    'multiple_assessments' => $stats['multiple_assessments'],
                    'relevance_for_different_levels' => $stats['relevance_for_different_levels'],
                    'rel_skipped' => $stats['rel_skipped'],
                    'base_lang_docs' => $stats['base_lang_docs'],
                    'finished_incl_multiple' => $stats['finished_incl_multiple'],
                    'total_incl_multiple' => $stats['total_incl_multiple'],
                    'finished_incl_multiple_prop' => $stats['finished_incl_multiple_prop']
                );
        } else if ($type == Constants::ASSESSMENT_MODULE_DETAILED_RESULTS) {
            return
                array(
                    'run_names_short' => $short_run_names,
                    'num_found' => $stats['num_found'],
                    'num_found_total' => $stats['num_found_total'],
                    'total_relevant' => $stats['total_relevant'],
                    'limit_result_list' => Metrics::getLimitedResultList(),
                    'maximum_length_result_list' => Metrics::getMaximumLengthResultList(),
                    'invalid_value' => Constants::INVALID_VALUE,
                    'r' => $stats['r'],
                    'queries' => $stats['queries'],
                    'query_ids' => $stats['query_ids'],
                    'metrics_values' => $stats['final_metrics_values'],
                    'avg_metrics_values' => $stats['avg_values'],
                    'active_metrics' => $active_metrics
                );
        } else {
            throw new InvalidParameterException("Wrong module parameter!");
        }
    }

}
