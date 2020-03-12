<?php

namespace App\Entity\ComparisonModule\Configuration;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="ComparisonModule_StandaloneConfiguration")
 */
class StandaloneConfiguration
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
     * @ORM\Column(type="string", length=50)
     * Description of the evaluation buttons on the comparison page
     */
    private $evalButton_left;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $evalButton_right;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $middleButton;



    /**
     * @ORM\Column(type="text")
     * Base URL of the website that is shown in the assessment frame
     */
    private $url;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


    /**
     * @ORM\Column(type="string")
     * Presentation mode for the assessment documents. Either via an iframe, image URL, or simply document info (title, author, abstract, keywords)
     */
    private $presentation_mode;

    /**
     * @return mixed
     */
    public function getPresentationMode()
    {
        return $this->presentation_mode;
    }

    /**
     * @param mixed $presentation_mode
     */
    public function setPresentationMode($presentation_mode)
    {
        $this->presentation_mode = $presentation_mode;
    }


    /**
     * @ORM\Column(type="integer")
     * Fields that are shown in assessment frontend if iframe not selected
     */
    private $presentationFields;

    /**
     * @return mixed
     */
    public function getPresentationFields()
    {
        return $this->presentationFields;
    }

    /**
     * @param mixed $presentationFields
     */
    public function setPresentationFields($presentationFields)
    {
        $this->presentationFields = $presentationFields;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Name for the first presentation field
     */
    private $presentation_field_name_1;

    /**
     * @return mixed
     */
    public function getPresentationFieldName1()
    {
        return $this->presentation_field_name_1;
    }

    /**
     * @param $presentation_field_name
     */
    public function setPresentationFieldName1($presentation_field_name)
    {
        $this->presentation_field_name_1 = $presentation_field_name;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Name for the second presentation field
     */
    private $presentation_field_name_2;

    /**
     * @return mixed
     */
    public function getPresentationFieldName2()
    {
        return $this->presentation_field_name_2;
    }

    /**
     * @param $presentation_field_name
     */
    public function setPresentationFieldName2($presentation_field_name)
    {
        $this->presentation_field_name_2 = $presentation_field_name;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Name for the third presentation field
     */
    private $presentation_field_name_3;

    /**
     * @return mixed
     */
    public function getPresentationFieldName3()
    {
        return $this->presentation_field_name_3;
    }

    /**
     * @param $presentation_field_name
     */
    public function setPresentationFieldName3($presentation_field_name)
    {
        $this->presentation_field_name_3 = $presentation_field_name;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Name for the fourth presentation field
     */
    private $presentation_field_name_4;

    /**
     * @return mixed
     */
    public function getPresentationFieldName4()
    {
        return $this->presentation_field_name_4;
    }

    /**
     * @param $presentation_field_name
     */
    public function setPresentationFieldName4($presentation_field_name)
    {
        $this->presentation_field_name_4 = $presentation_field_name;
    }


    /**
     * @ORM\Column(type="boolean")
     */
    private $allow_tie;

    /**
     * @return mixed
     */
    public function getAllowTie()
    {
        return $this->allow_tie;
    }

    /**
     * @param $allow_tie
     */
    public function setAllowTie($allow_tie)
    {
        $this->allow_tie = $allow_tie;
    }


    /**
     * @return mixed
     */
    public function getEvalButtonLeft()
    {
        return $this->evalButton_left;
    }

    /**
     * @param mixed $evalButton_left
     */
    public function setEvalButtonLeft($evalButton_left)
    {
        $this->evalButton_left = $evalButton_left;
    }

    /**
     * @return mixed
     */
    public function getEvalButtonRight()
    {
        return $this->evalButton_right;
    }

    /**
     * @param mixed $evalButton_right
     */
    public function setEvalButtonRight($evalButton_right)
    {
        $this->evalButton_right = $evalButton_right;
    }

    /**
     * @return mixed
     */
    public function getMiddleButton()
    {
        return $this->middleButton;
    }

    /**
     * @param mixed $middleButton
     */
    public function setMiddleButton($middleButton)
    {
        $this->middleButton = $middleButton;
    }


    /**
     * @ORM\Column(type="boolean", nullable=true)
     * Random presentation of query/document combinations or grouping by query
     * (i.e., all documents for a query are display one after another)
     */
    private $group_by;

    /**
     * @return mixed
     */
    public function getGroupBy()
    {
        return $this->group_by;
    }

    /**
     * @param mixed $group_by
     */
    public function setGroupBy($group_by)
    {
        $this->group_by = $group_by;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Random presentation of query/document combinations or grouping by query
     * (i.e., all documents for a query are display one after another)
     */
    private $group_by_category;

    /**
     * @return mixed
     */
    public function getGroupByCategory()
    {
        return $this->group_by_category;
    }

    /**
     * @param mixed $group_by_category
     */
    public function setGroupByCategory($group_by_category)
    {
        $this->group_by_category = $group_by_category;
    }


    /**
     * @ORM\Column(type="boolean")
     */
    private $randomization;

    /**
     * @return mixed
     */
    public function getRandomization()
    {
        return $this->randomization;
    }

    /**
     * @param mixed $randomization
     */
    public function setRandomization($randomization)
    {
        $this->randomization = $randomization;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $document_order;

    /**
     * @return mixed
     */
    public function getDocumentOrder()
    {
        return $this->document_order;
    }

    /**
     * @param mixed $document_order
     */
    public function setDocumentOrder($document_order)
    {
        $this->document_order = $document_order;
    }

}
