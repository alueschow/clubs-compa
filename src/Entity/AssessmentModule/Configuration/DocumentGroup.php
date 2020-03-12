<?php

namespace App\Entity\AssessmentModule\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity
 * @ORM\Table(name="AssessmentModule_DocumentGroup")
 * @UniqueEntity(fields="name", message="Name already taken")
 * @UniqueEntity(fields="short_name", message="Short name already taken")
 */
class DocumentGroup
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
     * @ORM\Column(type="string", unique=true)
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
     * @ORM\Column(type="integer")
     */
    private $nrOfMaxEvaluations;

    /**
     * @return mixed
     */
    public function getNrOfMaxEvaluations()
    {
        return $this->nrOfMaxEvaluations;
    }

    /**
     * @param mixed $nrOfMaxEvaluations
     */
    public function setNrOfMaxEvaluations($nrOfMaxEvaluations)
    {
        $this->nrOfMaxEvaluations = $nrOfMaxEvaluations;
    }

}