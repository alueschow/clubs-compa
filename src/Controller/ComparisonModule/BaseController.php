<?php

namespace App\Controller\ComparisonModule;

use App\Entity\ComparisonModule\Configuration\MainConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Class BaseController
 * Includes the most basic controller for the Comparison module.
 *
 * Additionally holds functions for accessing configuration information from the database.
 *
 * @package App\Controller\ComparisonModule
 */
class BaseController extends AbstractController
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

}