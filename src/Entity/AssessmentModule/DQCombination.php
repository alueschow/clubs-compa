<?php

namespace App\Entity\AssessmentModule;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentModule\DQCombinationRepository")
 * @ORM\Table(name="AssessmentModule_DQCombination",uniqueConstraints={@ORM\UniqueConstraint(name="search_idx", columns={"document", "query"})})
 */
class DQCombination
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
     * @ORM\Column(type="integer")
     */
    private $evaluated;

    /**
     * @return mixed
     */
    public function getEvaluated()
    {
        return $this->evaluated;
    }

    /**
     * @param mixed $evaluated
     */
    public function setEvaluated($evaluated)
    {
        $this->evaluated = $evaluated;
    }


    /**
     * @ORM\Column(type="boolean")
     */
    private $skipped;

    /**
     * @return mixed
     */
    public function isSkipped()
    {
        return $this->skipped;
    }

    /**
     * @param mixed $skipped
     */
    public function setSkip($skipped)
    {
        $this->skipped = $skipped;
    }


    /**
     * @ORM\Column(type="boolean")
     */
    private $postponed;

    /**
     * @return mixed
     */
    public function isPostponed()
    {
        return $this->postponed;
    }

    /**
     * @param mixed $postponed
     */
    public function setPostponed($postponed)
    {
        $this->postponed = $postponed;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $skip_reason;

    /**
     * @return mixed
     */
    public function getSkipReason()
    {
        return $this->skip_reason;
    }

    /**
     * @param mixed $skip_reason
     */
    public function setSkipReason($skip_reason)
    {
        $this->skip_reason = $skip_reason;
    }

}