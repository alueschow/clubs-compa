<?php

namespace App\Entity\AssessmentModule\Configuration;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="AssessmentModule_RatingOption", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="search_idx", columns={"name"}),
 *     @ORM\UniqueConstraint(name="search_idx2", columns={"priority"}),
 *     @ORM\UniqueConstraint(name="search_idx3", columns={"short_name"})
 * })
 */
class RatingOption
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
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @ORM\Column(type="string")
     */
    private $short_name;

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * @param mixed $short_name
     */
    public function setShortName($short_name)
    {
        $this->short_name = $short_name;
    }


    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }


    /**
     * @ORM\Column(type="boolean")
     */
    private $used_in_metrics;

    /**
     * @return mixed
     */
    public function getUsedInMetrics()
    {
        return $this->used_in_metrics;
    }

    /**
     * @param mixed $used_in_metrics
     */
    public function setUsedInMetrics($used_in_metrics)
    {
        $this->used_in_metrics = $used_in_metrics;
    }

}