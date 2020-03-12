<?php

namespace App\Controller\ComparisonModule;

use App\Constants;
use App\Entity\ComparisonModule\Configuration\LiveConfiguration;
use App\Entity\ComparisonModule\Configuration\StandaloneConfiguration;
use App\Services\ComparisonStatistics;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ResultsController extends BaseController
{
    /**
     * @Route("/ComparisonModule/showResults", name="showComparisonResults")
     *
     * @param ComparisonStatistics $statistics
     * @return Response
     */
    public function showComparisonResultsAction(ComparisonStatistics $statistics) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // get basic statistics
        $basic = $statistics->getBasicStatistics();
        $data = $this->createDataArray($basic);
        $data['allow_tie_live'] = $this->getDoctrine()->getRepository(LiveConfiguration::class)->find(1)->getAllowTie();
        $data['allow_tie_standalone'] = $this->getDoctrine()->getRepository(StandaloneConfiguration::class)->find(1)->getAllowTie();

        return $this->render('comparison_module/results/results.html.twig', $data);
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
     * @param $stats
     * @return array
     */
    private function createDataArray($stats) {
        return
            array(
                'total_evaluations' => $stats['total_evaluations'],
                'all_document_names' => $stats['all_document_names'],
                'all_website_names' => $stats['all_website_names'],
                'pref_values' => $stats['pref_values'],
                'non_pref_values' => $stats['non_pref_values'],
                'pref_prop' => $stats['pref_prop'],
                'non_pref_prop' => $stats['non_pref_prop'],
                'ratio' => $stats['ratio'],
                'live_total_evaluations' => $stats['live_total_evaluations'],
                'live_pref_values' => $stats['live_pref_values'],
                'live_non_pref_values' => $stats['live_non_pref_values'],
                'live_pref_prop' => $stats['live_pref_prop'],
                'live_non_pref_prop' => $stats['live_non_pref_prop'],
                'live_ratio' => $stats['live_ratio'],
                'no_pref_live' => $stats['no_pref_live'],
                'no_pref_standalone' => $stats['no_pref_standalone'],
            );
    }

}