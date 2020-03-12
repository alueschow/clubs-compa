<?php

namespace App\Entity\AssessmentModule\Metrics;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="AssessmentModule_MetricConfiguration")
 */
class MetricConfiguration
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
     * @ORM\Column(type="boolean")
     */
    private $limited;

    /**
     * @return mixed
     */
    public function getLimited()
    {
        return $this->limited;
    }

    /**
     * @param mixed $limited
     */
    public function setLimited($limited)
    {
        $this->limited = $limited;
    }


    /**
     * @ORM\Column(type="integer")
     */
    private $max_length;

    /**
     * @return mixed
     */
    public function getMaxLength()
    {
        return $this->max_length;
    }

    /**
     * @param mixed $max_length
     */
    public function setMaxLength($max_length)
    {
        $this->max_length = $max_length;
    }


    /**
     * @ORM\Column(type="integer")
     */
    private $round_precision;

    /**
     * @return mixed
     */
    public function getRoundPrecision()
    {
        return $this->round_precision;
    }

    /**
     * @param mixed $round_precision
     */
    public function setRoundPrecision($round_precision)
    {
        $this->round_precision = $round_precision;
    }
}