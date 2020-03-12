<?php

namespace App\Entity\AssessmentModule;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentModule\AssessmentRepository")
 * @ORM\Table(name="AssessmentModule_Assessment",uniqueConstraints={@ORM\UniqueConstraint(name="search_idx", columns={"document", "query", "assessor"})})
 */
class Assessment
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
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentModule\Document")
     * @ORM\JoinColumn(name="document", referencedColumnName="doc_id")
     */
    private $document;

    /**
     * @return mixed
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param mixed $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentModule\Query")
     * @ORM\JoinColumn(name="query", referencedColumnName="query_id")
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
    public $rating;


    /**
     * @return mixed
     */

    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }


    /**
     * @ORM\Column(type="string")
     */
    public $assessor;


    /**
     * @return mixed
     */

    public function getAssessor()
    {
        return $this->assessor;
    }

    /**
     * @param mixed $assessor
     */
    public function setAssessor($assessor)
    {
        $this->assessor = $assessor;
    }

}