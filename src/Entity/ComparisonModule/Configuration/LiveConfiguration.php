<?php

namespace App\Entity\ComparisonModule\Configuration;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="ComparisonModule_LiveConfiguration")
 */
class LiveConfiguration
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
     * @ORM\Column(type="boolean")
     * Set randomization to TRUE or FALSE
     */
    private $randomization;

    /**
     * @ORM\Column(type="boolean")
     * Set randomization to TRUE or FALSE
     */
    private $randomization_participation;

    /**
     * @ORM\Column(type="string", length=50)
     * Set cookie values
     */
    private $cookie_name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $cookie_expires; // expire time in days

    /**
     * @ORM\Column(type="smallint")
     */
    private $participations_per_time_span; // number of maximum participations for a single user per time span

    /**
     * @ORM\Column(type="smallint")
     */
    private $time_span; // time span in hours


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
     * @ORM\Column(type="boolean")
     */
    private $use_base_website;

    /**
     * @return mixed
     */
    public function getUseBaseWebsite()
    {
        return $this->use_base_website;
    }

    /**
     * @param $use_base_website
     */
    public function setUseBaseWebsite($use_base_website)
    {
        $this->use_base_website = $use_base_website;
    }


    /**
     * @ORM\Column(type="string")
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
     * @param $document_order
     */
    public function setDocumentOrder($document_order)
    {
        $this->document_order = $document_order;
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
     * @return mixed
     */
    public function getRandomizationParticipation()
    {
        return $this->randomization_participation;
    }

    /**
     * @param mixed $randomization_participation
     */
    public function setRandomizationParticipation($randomization_participation)
    {
        $this->randomization_participation = $randomization_participation;
    }

    /**
     * @return mixed
     */
    public function getCookieName()
    {
        return $this->cookie_name;
    }

    /**
     * @param mixed $cookie_name
     */
    public function setCookieName($cookie_name)
    {
        $this->cookie_name = $cookie_name;
    }

    /**
     * @return mixed
     */
    public function getCookieExpires()
    {
        return $this->cookie_expires;
    }

    /**
     * @param mixed $cookie_expires
     */
    public function setCookieExpires($cookie_expires)
    {
        $this->cookie_expires = $cookie_expires;
    }

    /**
     * @return mixed
     */
    public function getParticipationsPerTimeSpan()
    {
        return $this->participations_per_time_span;
    }

    /**
     * @param mixed $participations_per_time_span
     */
    public function setParticipationsPerTimeSpan($participations_per_time_span)
    {
        $this->participations_per_time_span = $participations_per_time_span;
    }

    /**
     * @return mixed
     */
    public function getTimeSpan()
    {
        return $this->time_span;
    }

    /**
     * @param mixed $time_span
     */
    public function setTimeSpan($time_span)
    {
        $this->time_span = $time_span;
    }

}