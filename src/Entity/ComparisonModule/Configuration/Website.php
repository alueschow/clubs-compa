<?php

namespace App\Entity\ComparisonModule\Configuration;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="ComparisonModule_Website")
 */
class Website
{
    public function __construct()
    {
        $this->shown = 0;
    }

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
     * @ORM\Column(type="string", unique=true, length=254)
     */
    private $website_name;

    /**
     * @return mixed
     */
    public function getWebsiteName()
    {
        return $this->website_name;
    }

    /**
     * @param mixed $website_name
     */
    public function setWebsiteName($website_name)
    {
        $this->website_name = $website_name;
    }


    /**
     * @ORM\Column(type="text")
     */
    private $website_url;

    /**
     * @return mixed
     */

    public function getWebsiteURL()
    {
        return $this->website_url;
    }

    /**
     * @param mixed $website_url
     */
    public function setWebsiteURL($website_url)
    {
        $this->website_url = $website_url;
    }


    /**
     * @ORM\Column(type="integer")
     */
    private $shown;

    /**
     * @return mixed
     */

    public function getShown()
    {
        return $this->shown;
    }

    /**
     * @param mixed $shown
     */
    public function setShown($shown)
    {
        $this->shown = $shown;
    }


    /**
     * @ORM\Column(type="boolean")
     */
    private $is_base_website;

    /**
     * @return mixed
     */

    public function getIsBaseWebsite()
    {
        return $this->is_base_website;
    }

    /**
     * @param mixed $is_base_website
     */
    public function setIsBaseWebsite($is_base_website)
    {
        $this->is_base_website = $is_base_website;
    }

}