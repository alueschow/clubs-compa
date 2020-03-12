<?php

namespace App\Controller\AssessmentModule\Metrics;

use App\Controller\AssessmentModule\BaseController;
use App\Entity\AssessmentModule\Metrics\MetricConfiguration;


/**
 * Class BaseController
 * Holds functions for accessing configuration information from the database.
 * $row == 1 gets the default configuration
 * $row == 2 gets the custom user configuration
 *
 * @package App\Controller\AssessmentModule\Metrics
 */
class BaseMetricsController extends BaseController
{
    public function getMetricLimited($default=false) {
        $default
            ? $row = 1
            : $row = 2;
        return $this->getConfig($row)->getLimited()
            ? "true"
            : "false";
    }

    public function getMetricMaxLength($default=false) {
        $default
            ? $row = 1
            : $row = 2;
        return $this->getConfig($row)->getMaxLength();
    }

    public function getMetricRoundPrecision($default=false) {
        $default
            ? $row = 1
            : $row = 2;
        return $this->getConfig($row)->getRoundPrecision();
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     * /

    /**
     * Get configuration from MetricConfiguration repository.
     *
     * @param int $row
     * @return object
     */

    private function getConfig($row=2) {
        $repository = $this->getDoctrine()->getRepository(MetricConfiguration::class);
        $configuration = $repository->find($row);

        return $configuration;
    }

}