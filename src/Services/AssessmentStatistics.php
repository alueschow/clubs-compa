<?php

namespace App\Services;

use App\Constants;
use App\DatabaseUtils;
use App\Entity\AssessmentModule\Configuration\RatingOption;
use App\Entity\AssessmentModule\Metrics\Metric;
use App\Entity\AssessmentModule\Query;
use App\Progress;
use App\Repository\AssessmentModule\AssessmentRepository;
use App\Repository\AssessmentModule\DQCombinationRepository;
use App\Repository\AssessmentModule\GeneralRepository;
use App\Repository\AssessmentModule\SearchResultRepository;
use App\Repository\ComparisonModule\ComparisonRepository;
use Doctrine\ORM\EntityManager;


/**
 * A service that calculates statistics. Data are fetched from database using Repositories,
 * statistics are calculated using specifications in Metrics class.
 *
 * @package App\Services
 */
class AssessmentStatistics
{
    private $em;
    private $metrics;
    private $g_repo;
    private $dq_repo;
    private $as_repo;
    private $pc_repo;
    private $sr_repo;


    public function __construct(EntityManager $em, Metrics $metrics, GeneralRepository $g_repo,
                                DQCombinationRepository $dq_repo, AssessmentRepository $as_repo,
                                ComparisonRepository $pc_repo, SearchResultRepository $sr_repo)
    {
        $this->em = $em;
        $this->metrics = $metrics;
        $this->g_repo = $g_repo;
        $this->dq_repo = $dq_repo;
        $this->as_repo = $as_repo;
        $this->pc_repo = $pc_repo;
        $this->sr_repo = $sr_repo;
    }


    /**
     * Delivers basic statistics for the desired module.
     *
     * @return array
     */
    public function getBasicStatistics() {
        return $this->getBasicAssessmentStatistics();
    }


    /**
     * Calculates more detailed statistics for the Assessment module.
     *
     * For every query in every run:
     * (a) Number of documents found
     * (b) Number of relevant documents
     * (c) R-precision, Prec@10, Recall, nDCG
     *
     * @param $active_metrics
     * @param $rating_levels
     * @param $run_names
     * @return array
     */
    public function getDetailedAssessmentStatistics($active_metrics, $rating_levels, $run_names)
    {
        $qs = $this->loadQueries();
        $queries = $qs['queries'];
        $query_ids = $qs['query_ids'];

        $metrics = $this->calculateMetrics(
            $query_ids,
            $active_metrics,
            $rating_levels,
            $run_names
        );

        // Array for resulting statistics
        $result_array = array(
            'queries' => $queries,
            'query_ids' => $query_ids,
            'r' => $metrics['r'],
            'total_relevant' => $metrics['total_relevant'],
            'num_found' => $metrics['num_found'],
            'num_found_total' => $metrics['num_found_total'],
            'final_metrics_values' => $metrics['final_metrics_values']
        );

        // Calculate average values for the metrics
        $result_array = $this->calculateAvgMetrics($result_array, $active_metrics, $run_names);

        return $result_array;
    }


    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Delivers basic statistics for the Assessment module.
     * (a) Total number of assessments
     * (b) Number of finished assessments
     * (c) Proportion for (a) and (b), and number of skipped documents
     * (d) Progress for single users
     * (e) Progress for single document groups
     * (f) Assessments by rating option
     * (g) Assessments by run
     *
     * @return array
     */
    private function getBasicAssessmentStatistics()
    {
        // get active runs, users, document groups, and rating options from database
        DatabaseUtils::setEntityManager($this->em);
        $active_run_names = DatabaseUtils::getActiveRuns()['run_names'];
        $active_user_names = DatabaseUtils::getActiveUsers()['active_user_names_without_admins'];
        $active_user_groups = DatabaseUtils::getActiveUsers()['active_user_groups_without_admins'];
        $active_doc_groups = DatabaseUtils::getDocumentGroups()['document_groups'];
        $active_doc_groups_short = DatabaseUtils::getDocumentGroups()['document_groups_short'];
        $rating_levels = DatabaseUtils::getRatingOptions();

        // calculate progress for users
        Progress::setEntityManager($this->em);
        $user_progress = $this->getProgressForUsers($active_user_names, $active_user_groups);

        // calculate number of total and finished DQCombinations (without multiple assessments)
        $progress_dq = $this->getProgressForDQCombinations();
        $total_assessments = $progress_dq['total'];
        $finished_assessments = $progress_dq['finished'];
        $assessment_prop = $progress_dq['proportion'];

        // calculate number of skipped documents
        $skipped = (float)$this->g_repo->assessmentByRating(Constants::SKIPPED)[0]["col_" . Constants::SKIPPED];

        // calculate progress for all document groups
        $progress_groups = $this->getProgressForDocGroups($active_doc_groups_short);
        $nr_evaluations = $progress_groups['nr_evaluations'];
        $doc_groups_assessments_total = $progress_groups['doc_groups_assessments_total'];
        $doc_groups_assessments = $progress_groups['doc_groups_assessments'];
        $doc_groups_assessments_complete = $progress_groups['doc_groups_assessments_complete'];

        // calculate progress for multiple assessments
        $mltple = $this->getProgressForMultipleAssessments(
            $active_doc_groups_short,
            $finished_assessments,
            $doc_groups_assessments_total,
            $doc_groups_assessments_complete
        );
        $total_incl_multiple = $mltple['total_incl_multiple'];
        $finished_incl_multiple = $mltple['finished_incl_multiple'];
        $finished_incl_multiple_prop = $mltple['finished_incl_multiple_prop'];

        // get ratings for all runs
        $run_ratings = $this->countRatingsForRuns(
            $this->g_repo,
            $rating_levels,
            $active_run_names
        );
        $run_data = $this->getNumbersForRuns($this->g_repo, $active_run_names);
        $run_nr_assessed = $run_data['assessed'];
        $run_nr_total = $run_data['total'];

        // calculate progress for different rating options in all runs
        /** @var $rel_lev RatingOption */
        $rating_for_different_levels = array();
        if ($rating_levels != null) {
            foreach ($rating_levels as $rel_lev) {
                $rating_for_different_levels[$rel_lev->getName()] =
                    (float)$this->g_repo->assessmentByRating(
                        $rel_lev->getName(),
                        $rel_lev->getShortName()
                    )[0]["col_" . $rel_lev->getShortName()];
            }
        }

        return array(
            'run_names' => $active_run_names,
            'user_names' => $active_user_names,
            'doc_groups_names' => $active_doc_groups,
            'doc_groups' => $active_doc_groups_short,
            'user_progress' => $user_progress,
            'total_assessments' => $total_assessments,
            'finished_assessments' => $finished_assessments,
            'assessment_prop' => $assessment_prop,
            'skipped' => $skipped,
            'nr_evaluations' => $nr_evaluations,
            'doc_groups_assessments_total' => $doc_groups_assessments_total,
            'doc_groups_assessments' => $doc_groups_assessments,
            'doc_groups_assessments_complete' => $doc_groups_assessments_complete,
            'total_incl_multiple' => $total_incl_multiple,
            'finished_incl_multiple' => $finished_incl_multiple,
            'finished_incl_multiple_prop' => $finished_incl_multiple_prop,
            'rating_levels_in_runs' => $run_ratings,
            'run_number_assessed' => $run_nr_assessed,
            'run_number_total' => $run_nr_total,
            'rating_for_different_levels' => $rating_for_different_levels
        );
    }


    /**
     * Calculate total number of DQCombinations and number of DQCombinations with at least 1 assessment.
     * Also get proportion of these values.
     *
     * @return array
     */
    private function getProgressForDQCombinations()
    {
        /* Get basic progress statistics (no multiple assessments considered yet) */
        // different DQCombinations
        $total_assessments = $this->dq_repo->findTotalAssessments();
        // number of different DQCombinations
        $total_assessments = (float)$total_assessments[0]['total_assessments'];
        // DQCombinations with at least 1 assessment
        $finished_assessments = $this->dq_repo->findFinishedAssessments();
        // number of DQCombinations with at least 1 assessment
        $finished_assessments = (float)$finished_assessments[0]['finished_assessments'];
        // calculate proportion
        $total_assessments > 0
            ? $assessment_prop = round(($finished_assessments / $total_assessments) * 100, 1)
            : $assessment_prop = 0;

        return array('total' => $total_assessments, 'finished' => $finished_assessments, 'proportion' => $assessment_prop);
    }


    /**
     * Calculate progress separately for each active document group.
     *
     * @param $active_doc_groups_short
     * @return array
     */
    private function getProgressForDocGroups($active_doc_groups_short) {
        $doc_groups_assessments = $doc_groups_assessments_complete = $doc_groups_assessments_total = $nr_evaluations = array();
        DatabaseUtils::setEntityManager($this->em);
        // iterate over all doc_groups
        if (!empty($active_doc_groups_short)) {
            foreach ($active_doc_groups_short as $group) {
                // maximum number of assessments for this group
                $nr_evaluations[$group] = DatabaseUtils::getNrOfMaxEvaluations($group);
                // total number of DQCombinations for this group
                $doc_groups_assessments_total[$group] = (float)$this->g_repo->assessmentByDocGroup($group, true)[0][$group . '_assessments'];
                // number of DQCombinations for this group that have at least 1 assessment
                $doc_groups_assessments[$group] = (float)$this->g_repo->assessmentByDocGroup($group, false, 1)[0][$group . '_assessments'];
                // number of DQCombinations for this group that are complete
                $doc_groups_assessments_complete[$group] = (float)$this->g_repo->assessmentByDocGroup($group, false, $nr_evaluations[$group])[0][$group . '_assessments'];
            }
        }

        return array(
            'nr_evaluations' => $nr_evaluations,
            'doc_groups_assessments_total' => $doc_groups_assessments_total,
            'doc_groups_assessments' => $doc_groups_assessments,
            'doc_groups_assessments_complete' => $doc_groups_assessments_complete
        );
    }


    /**
     * Calculate progress for DQCombinations, considering multiple assessments.
     *
     * @param $active_doc_groups_short
     * @param $finished_assessments
     * @param $doc_groups_assessments_total
     * @param $doc_groups_assessments_complete
     * @return array
     */
    private function getProgressForMultipleAssessments($active_doc_groups_short, $finished_assessments, $doc_groups_assessments_total, $doc_groups_assessments_complete) {
        DatabaseUtils::setEntityManager($this->em);
        $total_incl_multiple = 0;
        $finished_incl_multiple = $finished_assessments;

        if (!empty($active_doc_groups_short)) {
            foreach ($active_doc_groups_short as $group) {
                $nr_evaluations[$group] = DatabaseUtils::getNrOfMaxEvaluations($group);
                $total_incl_multiple += $doc_groups_assessments_total[$group] * $nr_evaluations[$group];

                // calculate finished DQCombinations, considering multiple assessments
                if ($nr_evaluations[$group] > 1) {
                    $finished_incl_multiple += $doc_groups_assessments_complete[$group];
                }

            }
        }
        // calculate proportion of finished multiple assessments
        $total_incl_multiple > 0
            ? $finished_incl_multiple_prop = round($finished_incl_multiple * 100 / $total_incl_multiple,1)
            : $finished_incl_multiple_prop = 0;

        return array(
            'total_incl_multiple' => $total_incl_multiple,
            'finished_incl_multiple' => $finished_incl_multiple,
            'finished_incl_multiple_prop' => $finished_incl_multiple_prop
        );
    }


    /** Iterate over every run and get number of entries for each rating option
     * (e.g. 'highly', 'partially', and 'non relevant')
     * @param GeneralRepository $g_repo
     * @param $levels
     * @param $runs
     * @return array
     */
    private function countRatingsForRuns(GeneralRepository $g_repo, $levels, $runs) {
        $rating_levels_in_runs = array();
        /** @var $lev RatingOption */
        foreach ($runs as $run) {
            foreach ($levels as $lev) {
                $level_rating = $g_repo->assessmentByRun("rating_levels", $run,$lev->getName());
                $rating_levels_in_runs[$lev->getName()][] = (float)$level_rating;
            }
        }
        return $rating_levels_in_runs;
    }


    /**
     * Get number of Assessments for each run,
     * and number of total SearchResults for each run.
     *
     * @param $g_repo
     * @param $runs
     * @return array
     */
    private function getNumbersForRuns($g_repo, $runs) {
        /** @var GeneralRepository $g_repo */
        $run_number_assessed = $run_number_total = array();
        foreach ($runs as $run) {
            $run_number_assessed[] =$g_repo->assessmentByRun('assessed', $run);
            $run_number_total[] = $g_repo->assessmentByRun('total', $run);
        }
        return array(
            'assessed' => $run_number_assessed,
            'total' => $run_number_total,
        );
    }


    /**
     * @param $names
     * @param $groups
     * @return array
     */
    private function getProgressForUsers($names, $groups) {
        if (sizeOf($names) != sizeOf($groups)) {
            throw new \InvalidArgumentException("User arrays do not have the same size!");
        }

        $user_progress = array();
        for ($i = 0; $i < count($names); $i++) {
            $user_progress[] = Progress::getUserProgress($names[$i],$groups[$i]);
        }

        return $user_progress;
    }


    /**
     * Load all queries, get query and query ID and save them in arrays.
     *
     * @return array
     */
    private function loadQueries() {
        $queries = $query_ids = array();
        $tmp_qs = $this->em->getRepository(Query::class)->findAll();
        /**
        * @var Query $q
        */
        foreach ($tmp_qs as $q) {
            array_push($queries, $q->getQuery());
            array_push($query_ids, $q->getQuery_Id());
        }
        unset($tmp_qs);
        return array('queries' => $queries, 'query_ids' => $query_ids);
    }


    /**
     * Calculate metrics for each query.
     * Iterate over all queries and for each query, iterate over all runs.
     * Get total number of relevant documents for the query and calculate metrics
     * based on the relevant documents found up to a certain rank.
     *
     * @param $query_ids
     * @param $active_metrics
     * @param $rating_levels
     * @param $run_names
     * @return array
     */
    private function calculateMetrics($query_ids, $active_metrics, $rating_levels, $run_names) {
        /* Variables */
        /** @var $metric Metric */
        $num_found_final = $num_found_total_final = $relevant_total = $relevant_limited = array();
        $metrics_values = array();  // array for metrics values, cleared after each run
        $final_metrics_values = array();  // array for metrics values; passed at then end of the function
        foreach ($active_metrics as $metric) {
            $final_metrics_values[$metric->getId()] = array();
        }

        /* Iterate over all queries */
        for ($i = 0; $i < count($query_ids); $i++) {
            /* define empty arrays that will hold the single metrics' values */
            foreach ($active_metrics as $metric) {
                $metrics_values[$metric->getId()] = array();
            }

            /* Get number of relevant documents for this query */
            $relevant = $this->countRelevantDocuments($query_ids[$i], $rating_levels);
            $relevant_total[] = $relevant['relevant_total'];
            $relevant_limited[] = $relevant['relevant_limited'];

            /* Iterate over single runs */
            $num_found_for_this_query = $num_found_for_this_query_total = array();
            foreach ($run_names as $run_id) {
                $ranked_docs = null;

                /* Get number of documents found for this query in this run.
                 $num_found_total holds the total number of documents found;
                 $num_found may be limited if the constant Metrics::getMaximumLengthResultList() is set. */
                $docs_found = $this->sr_repo
                    ->findTotalNumFoundForQueryInRun($query_ids[$i], $run_id);
                /* If no documents are found in a run, it is not reasonable to calculate metrics */
                if (empty($docs_found)) {
                    $num_found = Constants::INVALID_VALUE;
                    $num_found_total = Constants::INVALID_VALUE;
                    foreach ($active_metrics as $metric) {
                        $metrics_values[$metric->getId()][] = Constants::INVALID_VALUE;
                    }
                } else {  // documents were found
                    $num_found_total = $this->metrics->getNumberOfDocumentsFound($docs_found, false);
                    $num_found = $this->metrics->getNumberOfDocumentsFound($docs_found, true);
                    unset($docs_found);

                    /* Count number of relevant documents for specific ranks. */
                    $relevant_docs = $this->countRelevantDocumentsForRanks($query_ids[$i], $run_id,
                        $relevant_total[$i], $relevant_limited[$i]);
                    $relevant_docs_in_run = $relevant_docs['relevant_docs_in_run'];
                    $relevant_docs_up_to_rank_limit = $relevant_docs['relevant_docs_up_to_rank_limit'];
                    $relevant_docs_up_to_rank_relevant_total = $relevant_docs['relevant_docs_up_to_rank_relevant_total'];
                    $relevant_docs_up_to_rank_relevant_limited = $relevant_docs['relevant_docs_up_to_rank_relevant_limited'];

                    /* Call metric functions based on the number of found relevant documents */
                    if (Metrics::getLimitedResultList()) {
                        foreach ($active_metrics as $metric) {
                            /**
                             * R-Precision
                             */
                            if ($metric->getName() === Constants::R_PRECISION) {
                                $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::R_PRECISION,
                                    $relevant_docs_up_to_rank_relevant_limited,
                                    $relevant_limited[$i]);
                            }
                            /**
                             * Precision
                             */
                            if ($metric->getName() === Constants::PRECISION) {
                                $metric->getK() < 0
                                    ? $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::PRECISION,
                                    $relevant_docs_up_to_rank_limit,
                                    Metrics::getMaximumLengthResultList())
                                    : $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::PRECISION,
                                    $this->sr_repo->findRelevantDocsForQueryInRun(
                                        $query_ids[$i], $run_id, $metric->getK()
                                    )[0]['docs'],
                                    $metric->getK());

                            }
                            /**
                             * Recall
                             */
                            if ($metric->getName() === Constants::RECALL) {
                                $metric->getK() < 0
                                    ? $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::RECALL,
                                    $relevant_docs_in_run,
                                    $relevant_limited[$i])
                                    : $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::RECALL,
                                    $this->sr_repo->findRelevantDocsForQueryInRun(
                                        $query_ids[$i], $run_id, $metric->getK()
                                    )[0]['docs'],
                                    $relevant_limited[$i]);
                            }
                        }
                    } else {  // No limit set, use full result list
                        foreach ($active_metrics as $metric) {
                            /**
                             * R-Precision
                             */
                            if ($metric->getName() === Constants::R_PRECISION) {
                                $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::R_PRECISION,
                                    $relevant_docs_up_to_rank_relevant_total,
                                    $relevant_total[$i]);
                            }
                            /**
                             * Precision
                             */
                            if ($metric->getName() === Constants::PRECISION) {
                                $metric->getK() < 0
                                    ? $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::PRECISION,
                                    $relevant_docs_in_run,
                                    $num_found_total)
                                    : $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::PRECISION,
//                                    $relevant_docs_up_to_rank_precision_at,
                                    $this->sr_repo->findRelevantDocsForQueryInRun(
                                        $query_ids[$i], $run_id, $metric->getK()
                                    )[0]['docs'],
                                    $metric->getK());
                            }
                            /**
                             * Recall
                             */
                            if ($metric->getName() === Constants::RECALL) {
                                $metric->getK() < 0
                                    ? $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::RECALL,
                                    $relevant_docs_in_run,
                                    $relevant_total[$i])
                                    : $metrics_values[$metric->getId()][] = $this->callMetricForResultList(
                                    Constants::RECALL,
//                                    $relevant_docs_up_to_rank_recall_at,
                                    $this->sr_repo->findRelevantDocsForQueryInRun(
                                        $query_ids[$i], $run_id, $metric->getK()
                                    )[0]['docs'],
                                    $relevant_total[$i]);
                            }
                        }
                    }

                }  // End of single run

                /* Save data specific for this run in arrays */
                $num_found_for_this_query[] = $num_found;
                $num_found_for_this_query_total[] = $num_found_total;

            }  // End of single query

            /* Save data specific for this query in arrays. */
            $num_found_final[] = $num_found_for_this_query;
            $num_found_total_final[] = $num_found_for_this_query_total;
            foreach ($active_metrics as $metric) {
                $final_metrics_values[$metric->getId()][] = $metrics_values[$metric->getId()];
            }

        }  // End of iteration over all queries

        return array('total_relevant' => $relevant_total, 'r' => $relevant_limited, 'num_found' => $num_found_final,
            'num_found_total' => $num_found_total_final, 'final_metrics_values' => $final_metrics_values);
    }


    /**
     * Get number of relevant documents for this query. This number is calculated across all runs.
     * $relevant_total holds the total number of documents that are rated relevant for this query;
     * $relevant_limited holds the total number of relevant documents but is potentially
     * limited to the value defined in Metrics::getMaximumLengthResultList().
     *
     * @param $query
     * @param $rating_levels
     * @return array
     */
    private function countRelevantDocuments($query, $rating_levels)
    {
        $relevant_documents = array();
        /**
         * @var $level RatingOption
         */
        foreach ($rating_levels as $level) {
            $relevant_documents[] = $this->as_repo->findRelevant($query, $level->getName());
        }
        $relevant_docs_for_query = $this->metrics->totalRelevantDocs($relevant_documents);
        $relevant_total = $relevant_docs_for_query;
        $relevant_limited = $relevant_docs_for_query;
        if (Metrics::getLimitedResultList() && $relevant_docs_for_query > Metrics::getMaximumLengthResultList())
            $relevant_limited = Metrics::getMaximumLengthResultList();

        return array('relevant_total' => $relevant_total, 'relevant_limited' => $relevant_limited);
    }


    /**
     * Get number of __relevant__ documents that were found for this query in this run.
     * Different metrics need different values, so calculate number of relevant documents
     * up to different ranks.
     *
     * @param $query
     * @param $run
     * @param $relevant_total
     * @param $relevant_limited
     * @return array
     */
    private function countRelevantDocumentsForRanks($query, $run, $relevant_total, $relevant_limited) {
        // Total number of relevant documents found in the whole run
        $relevant_docs_in_run = $this->sr_repo
            ->findRelevantDocsForQueryInRun($query, $run)[0]['docs'];

        // Number of relevant document found up to the limit as defined in Metrics::getMaximumLengthResultList()
        $relevant_docs_up_to_rank_limit = $this->sr_repo
            ->findRelevantDocsForQueryInRun($query, $run, Metrics::getMaximumLengthResultList())[0]['docs'];

        // Number of relevant documents found up to the rank that corresponds to the total number of relevant
        // documents found for this run
        $relevant_docs_up_to_rank_relevant_total = $this->sr_repo
            ->findRelevantDocsForQueryInRun($query, $run, $relevant_total)[0]['docs'];

        // Number of relevant documents found up to the rank that corresponds to the limited number of relevant
        // documents found for this run (may be equal to relevant_total)
        $relevant_docs_up_to_rank_relevant_limited = $this->sr_repo
            ->findRelevantDocsForQueryInRun($query, $run, $relevant_limited)[0]['docs'];

        return array('relevant_docs_in_run' => $relevant_docs_in_run,
            'relevant_docs_up_to_rank_limit' => $relevant_docs_up_to_rank_limit,
            'relevant_docs_up_to_rank_relevant_total' => $relevant_docs_up_to_rank_relevant_total,
            'relevant_docs_up_to_rank_relevant_limited' => $relevant_docs_up_to_rank_relevant_limited
        );
    }


    /**
     * Call the right metric function.
     *
     * @param $name
     * @param $nr_docs
     * @param $denominator
     * @return int
     */
    private function callMetricForResultList($name, $nr_docs, $denominator) {
        $metric_result = null;

        if ($name == Constants::R_PRECISION) {
            $metric_result = $this->metrics->rprecision($nr_docs, $denominator);
        } else if ($name == Constants::PRECISION) {
            $metric_result = $this->metrics->precision($nr_docs, $denominator);
        } else if ($name == Constants::RECALL) {
            $metric_result = $this->metrics->recall($nr_docs, $denominator);
        }

        return $metric_result;
    }


    /**
     * Calculate avg values for each metric.
     *
     * @param $result_array
     * @param $active_metrics
     * @param $run_names
     * @return mixed
     */
    private function calculateAvgMetrics($result_array, $active_metrics, $run_names)
    {
        /**
         * @var $metric Metric
         */
        foreach ($active_metrics as $metric) {
            $result_array['avg_values'][$metric->getId() . "_avg"] =
                $this->metrics->getAvg(
                    $metric->getId(),
                    $result_array['final_metrics_values'],
                    $run_names
                );
        }

        return $result_array;
    }
}
