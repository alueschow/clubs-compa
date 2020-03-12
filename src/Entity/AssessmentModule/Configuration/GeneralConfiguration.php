<?php

namespace App\Entity\AssessmentModule\Configuration;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="AssessmentModule_GeneralConfiguration")
 */
class GeneralConfiguration
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
     * @ORM\Column(type="text", nullable=true)
     * If only queries, only topics, or both is used
     */
    private $query_style;

    /**
     * @return mixed
     */
    public function getQueryStyle()
    {
        return $this->query_style;
    }

    /**
     * @param mixed $query_style
     */
    public function setQueryStyle($query_style)
    {
        $this->query_style = $query_style;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Name for the query heading
     */
    private $queryHeadingName;

    /**
     * @return mixed
     */
    public function getQueryHeadingName()
    {
        return $this->queryHeadingName;
    }

    /**
     * @param $queryHeadingName
     */
    public function setQueryHeadingName($queryHeadingName)
    {
        $this->queryHeadingName = $queryHeadingName;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Name for the topic heading
     */
    private $topicHeadingName;

    /**
     * @return mixed
     */
    public function getTopicHeadingName()
    {
        return $this->topicHeadingName;
    }

    /**
     * @param $topicHeadingName
     */
    public function setTopicHeadingName($topicHeadingName)
    {
        $this->topicHeadingName = $topicHeadingName;
    }


    /**
     * @ORM\Column(type="boolean")
     * If document heading is used
     */
    private $documentHeading;

    /**
     * @return mixed
     */
    public function getDocumentHeading()
    {
        return $this->documentHeading;
    }

    /**
     * @param mixed $documentHeading
     */
    public function setDocumentHeading($documentHeading)
    {
        $this->documentHeading = $documentHeading;
    }


    /**
     * @ORM\Column(type="string", nullable=true)
     * Name for the document heading
     */
    private $documentHeadingName;

    /**
     * @return mixed
     */
    public function getDocumentHeadingName()
    {
        return $this->documentHeadingName;
    }

    /**
     * @param $documentHeadingName
     */
    public function setDocumentHeadingName($documentHeadingName)
    {
        $this->documentHeadingName = $documentHeadingName;
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
     * @ORM\Column(type="string")
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
     * Defines whether documents can be skipped
     */
    private $skipping_allowed;

    /**
     * @return mixed
     */
    public function getSkippingAllowed()
    {
        return $this->skipping_allowed;
    }

    /**
     * @param mixed $skipping_allowed
     */
    public function setSkippingAllowed($skipping_allowed)
    {
        $this->skipping_allowed = $skipping_allowed;
    }


    /**
     * @ORM\Column(type="text", nullable=true)
     * Defines how documents can be skipped. "reject", "postpone", "both"
     */
    private $skipping_options;

    /**
     * @return mixed
     */
    public function getSkippingOptions()
    {
        return $this->skipping_options;
    }

    /**
     * @param mixed $skipping_options
     */
    public function setSkippingOptions($skipping_options)
    {
        $this->skipping_options = $skipping_options;
    }


    /**
     * @ORM\Column(type="boolean", nullable=true)
     * Defines if comments are allowed when skipping documents
     */
    private $skipping_comment;

    /**
     * @return mixed
     */
    public function getSkippingComment()
    {
        return $this->skipping_comment;
    }

    /**
     * @param mixed $skipping_comment
     */
    public function setSkippingComment($skipping_comment)
    {
        $this->skipping_comment = $skipping_comment;
    }


    /**
     * @ORM\Column(type="boolean")
     * Defines whether a button appears that allows to load a new document without skipping or postponing the old one
     */
    private $loading_new_document;

    /**
     * @return mixed
     */
    public function getLoadingNewDocument()
    {
        return $this->loading_new_document;
    }

    /**
     * @param mixed $loading_new_document
     */
    public function setLoadingNewDocument($loading_new_document)
    {
        $this->loading_new_document = $loading_new_document;
    }


    /**
     * @ORM\Column(type="boolean")
     * Defines whether a user's progress bar is shown
     */
    private $user_progress_bar;

    /**
     * @return mixed
     */
    public function getUserProgressBar()
    {
        return $this->user_progress_bar;
    }

    /**
     * @param mixed $user_progress_bar
     */
    public function setUserProgressBar($user_progress_bar)
    {
        $this->user_progress_bar = $user_progress_bar;
    }


    /**
     * @ORM\Column(type="boolean")
     * Defines whether the total's progress bar is shown
     */
    private $total_progress_bar;

    /**
     * @return mixed
     */
    public function getTotalProgressBar()
    {
        return $this->total_progress_bar;
    }

    /**
     * @param mixed $total_progress_bar
     */
    public function setTotalProgressBar($total_progress_bar)
    {
        $this->total_progress_bar = $total_progress_bar;
    }

    /**
     * @ORM\Column(type="string")
     * Heading for rating options
     */
    private $rating_heading;

    /**
     * @return mixed
     */
    public function getRatingHeading()
    {
        return $this->rating_heading;
    }

    /**
     * @param mixed $rating_heading
     */
    public function setRatingHeading($rating_heading)
    {
        $this->rating_heading = $rating_heading;
    }

}