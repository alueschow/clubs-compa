<?php

namespace App\Controller\AssessmentModule;

use App\Entity\AssessmentModule\Configuration\GeneralConfiguration;
use App\Entity\AssessmentModule\Configuration\MainConfiguration;


/**
 * Class BaseController
 *
 * Holds functions for accessing configuration information from the database.
 *
 * @package App\Controller\AssessmentModule
 */
class BaseController extends \App\Controller\BaseController
{
    /**
     * Functions for accessing configuration from the MainConfiguration database table
     */
    public function getActiveMode() {
        $repository = $this->getDoctrine()->getRepository(MainConfiguration::class);
        $configuration = $repository->find(1);
        return $configuration->getMode();
    }

    public function getActiveStudyCategory() {
        $repository = $this->getDoctrine()->getRepository(MainConfiguration::class);
        $configuration = $repository->find(1);
        return $configuration->getActiveStudyCategory();
    }

    public function getDebugModeStatus() {
        $repository = $this->getDoctrine()->getRepository(MainConfiguration::class);
        $configuration = $repository->find(1);
        return $configuration->getDebugModeStatus();
    }

    public function getUseMetricsStatus() {
        $repository = $this->getDoctrine()->getRepository(MainConfiguration::class);
        $configuration = $repository->find(1);
        return $configuration->getUseMetricsStatus();
    }



    /**
     * Functions for accessing configuration from the GeneralConfiguration database table
     */
    public function getAssessmentURL() {
        return $this->getConfig()->getURL();
    }

    public function getAssessmentPresentationMode() {
        return $this->getConfig()->getPresentationMode();
    }

    public function getPresentationFields() {
        return $this->getConfig()->getPresentationFields();
    }

    public function getPresentationFieldName1() {
        return $this->getConfig()->getPresentationFieldName1();
    }

    public function getPresentationFieldName2() {
        return $this->getConfig()->getPresentationFieldName2();
    }

    public function getPresentationFieldName3() {
        return $this->getConfig()->getPresentationFieldName3();
    }

    public function getPresentationFieldName4() {
        return $this->getConfig()->getPresentationFieldName4();
    }

    public function getQueryStyle() {
        return $this->getConfig()->getQueryStyle();
    }

    public function getQueryHeading() {
        return $this->getConfig()->getQueryHeading();
    }

    public function getQueryHeadingName() {
        return $this->getConfig()->getQueryHeadingName();
    }

    public function getTopicHeading() {
        return $this->getConfig()->getTopicHeading();
    }

    public function getTopicHeadingName() {
        return $this->getConfig()->getTopicHeadingName();
    }

    public function getDocumentHeading() {
        return $this->getConfig()->getDocumentHeading();
    }

    public function getDocumentHeadingName() {
        return $this->getConfig()->getDocumentHeadingName();
    }

    public function getGroupBy() {
        return $this->getConfig()->getGroupBy();
    }

    public function getGroupByCategory() {
        return $this->getConfig()->getGroupByCategory();
    }

    public function getSkippingAllowed() {
        return $this->getConfig()->getSkippingAllowed();
    }

    public function getSkippingOptions() {
        return $this->getConfig()->getSkippingOptions();
    }

    public function getSkippingComment() {
        return $this->getConfig()->getSkippingComment();
    }

    public function getLoadingNewDocument() {
        return $this->getConfig()->getLoadingNewDocument();
    }

    public function getUserProgressBar() {
        return $this->getConfig()->getUserProgressBar();
    }

    public function getTotalProgressBar() {
        return $this->getConfig()->getTotalProgressBar();
    }

    public function getRatingHeading() {
        return $this->getConfig()->getRatingHeading();
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     * /
     
    /**
     * Get configuration from GeneralConfiguration repository.
     *
     * @return object
     */
    private function getConfig() {
        $repository = $this->getDoctrine()->getRepository(GeneralConfiguration::class);
        // ID == 1 holds the configuration data
        $configuration = $repository->find(1);

        return $configuration;
    }

}