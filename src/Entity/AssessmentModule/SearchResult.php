<?php

namespace App\Entity\AssessmentModule;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentModule\SearchResultRepository")
 * @ORM\Table(name="AssessmentModule_SearchResult",uniqueConstraints={@ORM\UniqueConstraint(name="search_idx", columns={"run_id", "document", "query", "rank"})})
 */
class SearchResult
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
    private $run_id;

    /**
     * @return mixed
     */
    public function getRun_Id()
    {
        return $this->run_id;
    }

    /**
     * @param mixed $run_id
     */
    public function setRun_Id($run_id)
    {
        $this->run_id = $run_id;
    }


    /**
     * @ORM\Column(type="integer")
     */
    private $rank;

    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }


    /**
     * @ORM\Column(type="integer")
     */
    private $num_found;

    /**
     * @return mixed
     */
    public function getNum_Found()
    {
        return $this->num_found;
    }

    /**
     * @param mixed $num_found
     */
    public function setNum_Found($num_found)
    {
        $this->num_found = $num_found;
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

}