<?php

namespace App\Controller\ComparisonModule\StandaloneModule;

use App\Controller\ComparisonModule\BaseController;
use App\Entity\ComparisonModule\Configuration\StandaloneConfiguration;


/**
 * Class BaseController
 * Includes the most basic controller for the Comparison module.
 *
 * Additionally holds functions for accessing configuration information from the database.
 *
 * @package App\Controller\ComparisonModule
 */
class BaseStandaloneController extends BaseController
{
    /**
     * Functions for accessing configuration from the database
     */    
    public function getAllowTie() {
        return $this->getConfig()->getAllowTie();
    }

    public function getComparisonURL() {
        return $this->getConfig()->getURL();
    }

    public function getPresentationMode() {
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

    public function getEvalButtonLeft() {
        return $this->getConfig()->getEvalButtonLeft();
    }

    public function getEvalButtonRight() {
        return $this->getConfig()->getEvalButtonRight();
    }

    public function getMiddleButton() {
        return $this->getConfig()->getmiddleButton();
    }

    public function getGroupBy() {
        return $this->getConfig()->getGroupBy();
    }

    public function getGroupByCategory() {
        return $this->getConfig()->getGroupByCategory();
    }

    public function getRandomization() {
        return $this->getConfig()->getRandomization();
    }

    public function getDocumentOrder() {
        return $this->getConfig()->getDocumentOrder();
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     * /
     *
     * /**
     * Get user configuration from selected repository.
     *
     * @return object
     */

    private function getConfig() {
        return $this->getDoctrine()->getRepository(StandaloneConfiguration::class)->find(1);
    }

}