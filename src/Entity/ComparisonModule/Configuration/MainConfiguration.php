<?php

namespace App\Entity\ComparisonModule\Configuration;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="ComparisonModule_MainConfiguration")
 */
class MainConfiguration
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @ORM\Column(type="string")
     * Configuration or production mode
     */
    private $mode;

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }


    /**
     * @ORM\Column(type="string")
     * Currently active study category
     */
    private $active_study_category;

    /**
     * @return mixed
     */
    public function getActiveStudyCategory()
    {
        return $this->active_study_category;
    }

    /**
     * @param mixed $active_study_category
     */
    public function setActiveStudyCategory($active_study_category)
    {
        $this->active_study_category = $active_study_category;
    }


    /**
     * @ORM\Column(type="boolean")
     * Holds the status of the Debug mode
     */
    private $debug_mode_status;

    /**
     * @return mixed
     */
    public function getDebugModeStatus()
    {
        return $this->debug_mode_status;
    }

    /**
     * @param mixed $debug_mode_status
     */
    public function setDebugModeStatus($debug_mode_status)
    {
        $this->debug_mode_status = $debug_mode_status;
    }

}