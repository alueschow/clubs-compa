<?php

namespace App\Services;

use App\Entity\ComparisonModule\Configuration\Website;
use App\Entity\ComparisonModule\Document;
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
class ComparisonStatistics
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
        return $this->getBasicComparisonStatistics();

    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Delivers basic statistics for the PC module.
     * (a) Total number of evaluations
     * (b) Preference for URL 1
     * (c) Preference for URL 2
     * (d) Proportions for (b) and (c)
     *
     * @return array
     */
    private function getBasicComparisonStatistics() {
        // get raw values
        $total_evaluations = $this->pc_repo->findTotalEvaluations();
        $live_total_evaluations = $this->pc_repo->findTotalEvaluations(true);

        $display_values = $this->calculateValuesForDisplay($total_evaluations, $live_total_evaluations);

        return array(
            'all_document_names' => $display_values['all_document_names'],
            'all_website_names' => $display_values['all_website_names'],
            'total_evaluations' => $display_values['total_evaluations'],
            'pref_values' => $display_values['pref_values'],
            'non_pref_values' => $display_values['non_pref_values'],
            'pref_prop' => $display_values['pref_prop'],
            'non_pref_prop' => $display_values['non_pref_prop'],
            'ratio' => $display_values['ratio'],
            'live_total_evaluations' => $display_values['live_total_evaluations'],
            'live_pref_values' => $display_values['live_pref_values'],
            'live_non_pref_values' => $display_values['live_non_pref_values'],
            'live_pref_prop' => $display_values['live_pref_prop'],
            'live_non_pref_prop' => $display_values['live_non_pref_prop'],
            'live_ratio' => $display_values['live_ratio'],
            'no_pref_standalone' => $display_values['no_pref_standalone'],
            'no_pref_live' => $display_values['no_pref_live']
        );

    }


    /**
     * Calculate values for display in frontend based on Document statistics
     *
     * @param $total_evaluations
     * @param $live_total_evaluations
     * @return array
     */
    private function calculateValuesForDisplay($total_evaluations, $live_total_evaluations) {
        // get Document statistics
        $document_statistics = $this->getDocumentStatistics();
        $all_documents = $document_statistics['all_documents'];
        $all_document_names = $document_statistics['all_document_names'];
        $all_websites = $document_statistics['all_websites'];
        $all_website_names = $document_statistics['all_website_names'];
        $document_pref = $document_statistics['document_pref'];
        $document_non_pref = $document_statistics['document_non_pref'];
        $live_website_pref = $document_statistics['live_website_pref'];
        $live_website_non_pref = $document_statistics['live_website_non_pref'];
        $no_pref_standalone = $document_statistics['no_pref_standalone'];
        $no_pref_live = $document_statistics['no_pref_live'];

        // calculate values for display
        $pref_values = $non_pref_values = $pref_prop = $non_pref_prop = $ratio
            = $live_pref_values = $live_non_pref_values = $live_pref_prop = $live_non_pref_prop = $live_ratio = array();

        $total_evaluations = (float)$total_evaluations[0]['pref'];
        $live_total_evaluations = (float)$live_total_evaluations[0]['pref'];

        for ($i = 0; $i < count($all_documents); $i++) {
            $pref_values[] = (float)$document_pref[$i][0]['pref'];
            $non_pref_values[] = (float)$document_non_pref[$i][0]['other'];
            try {
                $pref_prop[] = round(($document_pref[$i][0]['pref'] / ($document_pref[$i][0]['pref'] + $document_non_pref[$i][0]['other'])) * 100, 1);
            } catch (\Exception $e) {
                $pref_prop[] = 0;
            }
            try {
                $non_pref_prop[] = round(($document_non_pref[$i][0]['other'] / ($document_pref[$i][0]['pref'] + $document_non_pref[$i][0]['other'])) * 100, 1);
            } catch (\Exception $e) {
                $non_pref_prop[] = 0;
            }
            try {
                $ratio[] = round($pref_values[$i] / $non_pref_values[$i], 1);
            } catch (\Exception $e) {
                $ratio[] = 0;
            }
        }

        for ($i = 0; $i < count($all_websites); $i++) {
            $live_pref_values[] = (float)$live_website_pref[$i][0]['pref'];
            $live_non_pref_values[] = (float)$live_website_non_pref[$i][0]['other'];
            try {
                $live_pref_prop[] = round(($live_website_pref[$i][0]['pref'] / ($live_website_pref[$i][0]['pref'] + $live_website_non_pref[$i][0]['other'])) * 100, 1);
            } catch (\Exception $e) {
                $live_pref_prop[] = 0;
            }
            try {
                $live_non_pref_prop[] = round(($live_website_non_pref[$i][0]['other'] / ($live_website_pref[$i][0]['pref'] + $live_website_non_pref[$i][0]['other'])) * 100, 1);
            } catch (\Exception $e) {
                $live_non_pref_prop[] = 0;
            }
            try {
                $live_ratio[] = round($live_pref_values[$i] / $live_non_pref_values[$i], 1);
            } catch (\Exception $e) {
                $live_ratio[] = 0;
            }
        }

        return array(
            'total_evaluations' => $total_evaluations,
            'all_document_names' => $all_document_names,
            'all_website_names' => $all_website_names,
            'pref_values' => $pref_values, 'non_pref_values' => $non_pref_values,
            'pref_prop' => $pref_prop, 'non_pref_prop' => $non_pref_prop,
            'ratio' => $ratio,
            'live_total_evaluations' => $live_total_evaluations,
            'live_pref_values' => $live_pref_values, 'live_non_pref_values' => $live_non_pref_values,
            'live_pref_prop' => $live_pref_prop, 'live_non_pref_prop' => $live_non_pref_prop,
            'live_ratio' => $live_ratio, 'no_pref_standalone' => $no_pref_standalone, 'no_pref_live' => $no_pref_live
        );
    }


    /**
     * Get preference and non-preference for each Document
     *
     * @return array
     */
    private function getDocumentStatistics() {
        $all_document_names = $document_pref = $document_non_pref = $live_document_pref
            = $live_document_non_pref = array();

        $all_documents = $this->em->getRepository(Document::class)->findAll();
        /** @var Document $document */
        foreach ($all_documents as $document) {
            $document_name = $document->getDoc_Id();
            $all_document_names[] = $document_name;
            $document_pref[] = $this->pc_repo->findDocumentPreferences($document_name);
            $document_non_pref[] = $this->pc_repo->findDocumentNonPreferences($document_name);
        }
        $no_pref_standalone = $this->pc_repo->findDocumentPreferences("none")[0]['pref'];

        $all_websites = $this->em->getRepository(Website::class)->findAll();
        /** @var Document $document */
        foreach ($all_websites as $website) {
            $website_name = $website->getWebsiteName();
            $all_website_names[] = $website_name;
            $live_website_pref[] = $this->pc_repo->findDocumentPreferences($website_name, true);
            $live_website_non_pref[] = $this->pc_repo->findDocumentNonPreferences($website_name, true);
        }
        $no_pref_live = $this->pc_repo->findDocumentPreferences("none", true)[0]['pref'];

        return array(
            'all_documents' => $all_documents, 'all_document_names' => $all_document_names,
            'all_websites' => $all_websites, 'all_website_names' => $all_website_names,
            'document_pref' => $document_pref, 'document_non_pref' => $document_non_pref,
            'live_website_pref' => $live_website_pref, 'live_website_non_pref' => $live_website_non_pref,
            'no_pref_standalone' => $no_pref_standalone, 'no_pref_live' => $no_pref_live
        );
    }

}
