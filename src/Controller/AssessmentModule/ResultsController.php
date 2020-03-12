<?php

namespace App\Controller\AssessmentModule;

use App\Constants;
use App\Entity\AssessmentModule\Metrics\MetricConfiguration;
use App\ResultsUtils;
use App\Services\AssessmentStatistics;
use App\Services\Metrics;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ResultsController extends BaseController
{
    /**
     * @Route("/AssessmentModule/showResults/{type}", name="showAssessmentResults")
     *
     * @param AssessmentStatistics $statistics
     * @param Metrics $metrics
     * @param $type
     * @return Response
     */
    public function showAssessmentResultsAction(AssessmentStatistics $statistics, Metrics $metrics, $type) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($type == 'detailed' and !$this->getUseMetricsStatus()) {
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));
        }

        // Calculate special statistics if desired
        if ($type == 'detailed') {
            ResultsUtils::setEntityManager($this->getDoctrine()->getManager());
            ResultsUtils::setActiveStudyCategory($this->getActiveStudyCategory());
            $metrics_config = $this->getDoctrine()->getRepository(MetricConfiguration::class)->find(2);
            ResultsUtils::setMetricsConfiguration($metrics_config);
            $data = ResultsUtils::getDetailedResults($statistics, $metrics);

            return $this->render('assessment_module/results/assessment_detailed_results.html.twig', $data);
        }

        // Get basic statistics otherwise
        ResultsUtils::setActiveStudyCategory($this->getActiveStudyCategory());
        $data = ResultsUtils::getBasicResults($statistics);

        return $this->render('assessment_module/results/assessment_results.html.twig', $data);
    }

}