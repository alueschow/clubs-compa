<?php

namespace App\Entity;

use App\Constants;
use Symfony\Component\Routing\Exception\InvalidParameterException;


/**
 * Object that holds information useful for debugging purposes.
 */
class Debug
{
    private $count_without_pp;
    private $count_only_pp;
    private $count_subset;
    private $ranks;
    private $run_ids;
    private $num_founds;
    private $evaluated;
    private $skips;

    /**
     * Debug constructor that sets all properties to null/invalid values.
     */
    public function __construct()
    {
        $this->count_without_pp = Constants::INVALID_VALUE;
        $this->count_only_pp = Constants::INVALID_VALUE;
        $this->count_subset = Constants::INVALID_VALUE;
    }

    /**
     * Static constructor helper method to create a new Debug object with specific parameters.
     *
     * @param $count_without_pp
     * @param $count_only_pp
     * @param $count_subset
     * @return Debug
     */
    public static function createWithCounts($count_without_pp,
                                            $count_only_pp,
                                            $count_subset) {
        $instance = new self();
        $instance->setVariables(
            $count_without_pp,
            $count_only_pp,
            $count_subset
        );

        return $instance;
    }

    /**
     * Setter and Getter methods
     */

    public function getRanks() {
        return $this->ranks;
    }

    public function getRunIds() {
        return $this->run_ids;
    }

    public function getNumFounds() {
        return $this->num_founds;
    }

    public function getEvaluated() {
        return $this->evaluated;
    }

    public function getSkips() {
        return $this->skips;
    }



    /**
     * @param int $nr of subset
     * @return int|InvalidParameterException
     */
    public function getCountSubset($nr=null) {
        if ($nr == "wo_pp") {
            return $this->count_without_pp;
        } else if ($nr == "only_pp") {
            return $this->count_only_pp;
        } else {
            return new InvalidParameterException("Invalid subset parameter!");
        }
    }


    /**
     * @param string $name
     * @param $obj
     * @return void
     */
    public function add($name=null, $obj) {
        if ($name == 'ranks') {
            $this->ranks = $obj;
        } else if ($name == 'run_ids') {
            $this->run_ids = $obj;
        } else if ($name == 'num_founds') {
            $this->num_founds = $obj;
        } else if ($name == 'evaluated') {
            $this->evaluated = $obj;
        } else if ($name == 'skips') {
            $this->skips = $obj;
        } else {
            throw new InvalidParameterException("Invalid name parameter!");
        }
    }


    /**
     * ***********************************
     * *********** PRIVATE ***************
     * ********** FUNCTIONS **************
     * ***********************************
     */

    /**
     * @param $a
     * @param $b
     * @param $c
     */
    private function setVariables($a, $b, $c) {
        $this->count_without_pp = $a;
        $this->count_only_pp = $b;
        $this->count_subset = $c;
    }

}