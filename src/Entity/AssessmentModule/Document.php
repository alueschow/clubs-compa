<?php

namespace App\Entity\AssessmentModule;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="AssessmentModule_Document")
 */
class Document
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     */
    private $doc_id;

    /**
     * @return mixed
     */

    public function getDoc_Id()
    {
        return $this->doc_id;
    }

    /**
     * @param mixed $doc_id
     */
    public function setDoc_Id($doc_id)
    {
        $this->doc_id = $doc_id;
    }


    /**
     * @ORM\Column(type="string")
     */
    private $doc_group;

    /**
     * @return mixed
     */
    public function getDoc_Group()
    {
        return $this->doc_group;
    }

    /**
     * @param mixed $doc_group
     */
    public function setDoc_Group($doc_group)
    {
        $this->doc_group = $doc_group;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $field_1;

    /**
     * @return mixed
     */
    public function getField_1()
    {
        return $this->field_1;
    }

    /**
     * @param mixed $field_1
     */
    public function setField_1($field_1)
    {
        $this->field_1 = $field_1;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $field_2;

    /**
     * @return mixed
     */
    public function getField_2()
    {
        return $this->field_2;
    }

    /**
     * @param mixed $field_2
     */
    public function setField_2($field_2)
    {
        $this->field_2 = $field_2;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $field_3;

    /**
     * @return mixed
     */
    public function getField_3()
    {
        return $this->field_3;
    }

    /**
     * @param mixed $field_3
     */
    public function setField_3($field_3)
    {
        $this->field_3 = $field_3;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $field_4;

    /**
     * @return mixed
     */
    public function getField_4()
    {
        return $this->field_4;
    }

    /**
     * @param mixed $field_4
     */
    public function setField_4($field_4)
    {
        $this->field_4 = $field_4;
    }

}