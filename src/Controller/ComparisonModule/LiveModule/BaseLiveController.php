<?php

namespace App\Controller\ComparisonModule\LiveModule;

use App\Controller\ComparisonModule\BaseController;
use App\Entity\ComparisonModule\Configuration\LiveConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class BaseController
 * Includes the most basic controller for the Comparison module.
 *
 * Additionally holds functions for accessing configuration information from the database.
 *
 * @package App\Controller\ComparisonModule
 */
class BaseLiveController extends BaseController
{
    /**
     * @Route("/comparison", name="comparison")
     *
     * @param Request $request
     * @return Response
     */
    public function comparisonAction(Request $request)
    {
        return $this->redirectToRoute("comparison_standalone");
    }


    /**
     * Functions for accessing configuration from the database
     */
    public function getAllowTie() {
        return $this->getConfig()->getAllowTie();
    }

    public function getUseBaseWebsite() {
        return $this->getConfig()->getUseBaseWebsite();
    }

    public function getDocumentOrder() {
        return $this->getConfig()->getDocumentOrder();
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

    public function getRandomization() {
        return $this->getConfig()->getRandomization();
    }

    public function getRandomizationParticipation() {
        return $this->getConfig()->getRandomizationParticipation();
    }

    public function getCookieName() {
        return $this->getConfig()->getCookieName();
    }

    public function getCookieExpireTime() {
        return $this->getConfig()->getCookieExpires();
    }

    public function getParticipationsPerTimeSpan() {
        return $this->getConfig()->getParticipationsPerTimeSpan();
    }

    public function getTimeSpan() {
        return $this->getConfig()->getTimeSpan();
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     * /

    /**
     * Get user configuration from selected repository.
     *
     * @return object
     */

    private function getConfig() {
        return $this->getDoctrine()->getRepository(LiveConfiguration::class)->find(1);
    }

}