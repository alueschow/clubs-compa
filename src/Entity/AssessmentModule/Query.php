<?php

namespace App\Entity\AssessmentModule;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentModule\QueryRepository")
 * @ORM\Table(name="AssessmentModule_Query")
 */
class Query
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $query_id;

    /**
     * @return mixed
     */
    public function getQuery_Id()
    {
        return $this->query_id;
    }

    /**
     * @param mixed $id
     */
    public function setQuery_Id($id)
    {
        $this->query_id = $id;
    }

    /**
     * @ORM\Column(type="string")
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
    private $description;

    /**
     * @return mixed
     */

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

}