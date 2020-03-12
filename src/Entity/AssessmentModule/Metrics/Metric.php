<?php

namespace App\Entity\AssessmentModule\Metrics;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="AssessmentModule_Metric", uniqueConstraints={@ORM\UniqueConstraint(name="search_idx", columns={"name", "for_complete_list", "k"})}))
 */
class Metric
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $for_complete_list;

    /**
     * @ORM\Column(type="integer")
     */
    private $k;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;


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
     * @return mixed
     */
    public function getForCompleteList()
    {
        return $this->for_complete_list;
    }

    /**
     * @param mixed $for_complete_list
     */
    public function setForCompleteList($for_complete_list)
    {
        $this->for_complete_list = $for_complete_list;
    }

    /**
     * @return mixed
     */
    public function getK()
    {
        return $this->k;
    }

    /**
     * @param mixed $k
     */
    public function setK($k)
    {
        $this->k = $k;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}