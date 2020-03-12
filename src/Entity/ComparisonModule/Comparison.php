<?php

namespace App\Entity\ComparisonModule;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ComparisonModule\ComparisonRepository")
 * @ORM\Table(name="ComparisonModule_Comparison")
 */
class Comparison
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $query;

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $preferred_document;

    /**
     * @return mixed
     */

    public function getPreferred_Document()
    {
        return $this->preferred_document;
    }

    /**
     * @param mixed $preferred_document
     */
    public function setPreferred_Document($preferred_document)
    {
        $this->preferred_document = $preferred_document;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $other_document;

    /**
     * @return mixed
     */

    public function getOther_Document()
    {
        return $this->other_document;
    }

    /**
     * @param mixed $other_document
     */
    public function setOther_Document($other_document)
    {
        $this->other_document = $other_document;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $tester;

    /**
     * @return mixed
     */

    public function getTester()
    {
        return $this->tester;
    }

    /**
     * @param mixed $tester
     */
    public function setTester($tester)
    {
        $this->tester = $tester;
    }

}