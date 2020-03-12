<?php

namespace App\Services;

use App\Constants;
use App\Entity\AssessmentModule\Metrics\MetricConfiguration;


class Metrics
{
    private static $limited_result_list;
    private static $maximum_length_result_list;
    private static $round_precision;


    /**
     * Initialize Metrics service with data from configuration.
     *
     * @param MetricConfiguration $config
     */
    public static function init($config) {
        /** @var MetricConfiguration $config */
        self::setLimitedResultList($config->getLimited());
        self::setMaximumLengthResultList($config->getMaxLength());
        self::setRoundPrecision($config->getRoundPrecision());
    }

    public static function setLimitedResultList($value) {
        self::$limited_result_list = $value;
    }

    public static function getLimitedResultList() {
        return self::$limited_result_list;
    }

    public static function setMaximumLengthResultList($value) {
        self::$maximum_length_result_list = $value;
    }

    public static function getMaximumLengthResultList() {
        return self::$maximum_length_result_list;
    }

    public static function setRoundPrecision($value) {
        self::$round_precision = $value;
    }

    public static function getRoundPrecision() {
        return self::$round_precision;
    }


    /**
     * Set number of results based on total number of documents found.
     * $num_found has a maximum of $maximum_length_result_list for each run
     * if the $limited parameter is true.
     *
     * @param $docs_found
     * @param bool $limited
     * @return int
     */
    public static function getNumberOfDocumentsFound($docs_found, $limited=false)
    {
        if (!empty($docs_found[0])) {
            if ($limited) {
                if ($docs_found[0]['num_found'] > self::$maximum_length_result_list) {
                    $num_found = self::$maximum_length_result_list;
                } else if ($docs_found[0]['num_found'] == 0) {
                    // Happens if the Run ID in the foreach loop is empty or no results where found for this Run ID
                    $num_found = Constants::INVALID_VALUE;
                } else {
                    $num_found = $docs_found[0]['num_found'];
                }
            } else {  // not limited
                $num_found = $docs_found[0]['num_found'];
            }
        } else {
            // Happens with Run IDs that are defined in the configuration but not yet present in the databases
            $num_found = Constants::INVALID_VALUE;
        }

        return $num_found;
    }


    /**
     * Calculate R-precision (r divided by R).
     * R = total number of relevant documents
     * r = number of found relevant documents up to rank R
     *
     * @param $nr_of_retrieved_relevant_docs
     * @param $total_nr_of_relevant_documents
     * @return int
     */
    public static function rprecision($nr_of_retrieved_relevant_docs, $total_nr_of_relevant_documents)
    {
        return Metrics::metricsBaseFunction($nr_of_retrieved_relevant_docs, $total_nr_of_relevant_documents);
    }


    /**
     * Calculate Precision @ rank K
     * == number of found relevant documents divided by K.
     *
     * @param $nr_of_retrieved_relevant_docs
     * @param $at
     * @return int
     */
    public static function precision($nr_of_retrieved_relevant_docs, $at)
    {
        return Metrics::metricsBaseFunction($nr_of_retrieved_relevant_docs, $at);
    }


    /**
     * Calculate Recall(K).
     * (number of found relevant documents divided by total number of relevant documents found up to rank K)
     * Recall will be 1 if the result list contains all relevant documents.
     *
     * @param $nr_of_retrieved_relevant_docs
     * @param $total_nr_of_relevant_documents
     * @return int
     */
    public static function recall($nr_of_retrieved_relevant_docs, $total_nr_of_relevant_documents)
    {
        return Metrics::metricsBaseFunction($nr_of_retrieved_relevant_docs, $total_nr_of_relevant_documents);
    }


    /**
     * Determine number of relevance ratings.
     *
     * @param $relevance_level
     * @return int that represents the total number of distinct relevant documents
     */
    public static function totalRelevantDocs($relevance_level)
    {
        $docs = array();
        // Put relevant documents for each relevant level in an array and add only those
        // documents from other levels that are not already in the array
        foreach ($relevance_level as $level) {
            for ($i = 0; $i < count($level); $i++) {
                if(!in_array($level[$i]['document'], $docs)) {
                    $docs[] = $level[$i]['document'];
                }
            }
        }

        // Happens with queries that were not assessed yet
        if (count($docs) == 0) {
            $docs[] = strval(Constants::INVALID_VALUE);
        }

        // Set variables to 0 if no relevant docs found
        if ($docs[0] == strval(Constants::INVALID_VALUE)) {
            $r_total = 0;
        } else {
            $r_total = count($docs);
        }

        return $r_total;
    }


    /**
     * Calculate average value of a given metric for each run.
     *
     * @param $metric
     * @param $data
     * @param $run_names
     * @return array containing average value for each run
     */
    public static function getAvg($metric, $data, $run_names)
    {
        $avg = array();

        /* Iterate over each query for each run*/
        for($i = 0; $i < count($run_names); $i++) {
            $sum = 0.0;
            $not_considered = 0;
            foreach($data[$metric] as $querymetric) {
                if ($querymetric[$i] != Constants::INVALID_VALUE) {
                    $value_for_this_metric_in_the_current_query = $querymetric[$i];
                    $sum = $sum + $value_for_this_metric_in_the_current_query;
                } else {
                    $not_considered = $not_considered + 1;
                }
            }
            if ((count($data[$metric]) - $not_considered) > 0) {
                $avg[] = round($sum / (count($data[$metric]) - $not_considered), self::$round_precision);
            } else {
                $avg[] = Constants::INVALID_VALUE;
            }
        }
        return $avg;
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Simple division function that is used by some metrics.
     *
     * @param $a
     * @param $b
     * @return float|int
     */
    private static function metricsBaseFunction($a, $b) {
        return $b > 0
            ? round($a / $b, self::$round_precision)
            : Constants::INVALID_VALUE;
    }

}