<?php

namespace App\Repository\AssessmentModule;

use App\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;


class BaseAssessmentRepository extends BaseRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);
        $this->em = $em;
    }


    /**
     * Execute SQL query
     *
     * @return array
     */
    public function executeCustomSQL() {
        /* Build SELECT part */
        $finalselect = "";
        $select = array();
        foreach ($_GET['select'] as $selectedOption)
            array_push($select, $selectedOption);
        if (in_array('all', $select)) {
            $finalselect = "t.*";
        } else {
            foreach ($_GET['select'] as $selectedOption)
                if (strlen($finalselect) == 0) {
                    $finalselect =  't.' . $selectedOption;
                } else {
                    $finalselect =  $finalselect . ' ,t.' . $selectedOption;
                }
        }

        /* Build FROM part */
        $from = "AssessmentModule_" .  $_GET['table'];

        /* Build WHERE part */
        $where = "";
        if (isset($_GET['where_checked'])) {
            if (isset($_GET['where']) and isset($_GET['where_value'])) {
                $where_column = 't.' . $_GET['where'];
                $where_value = '';
                foreach ($_GET['where_value'] as $w)  // workaround that concatenates all $where_value parameters. Does not work if multiple forms where filled ...
                    $where_value = $where_value . $w;
                $where = $where_column . " = '" . $where_value . "'";
            }
        }

        /* Build ORDER BY part */
        $order = false;
        $orderby = "";
        $ordertype = "";
        if (isset($_GET['order_checked'])) {
            if (isset($_GET['orderby']) and isset($_GET['order_asc_desc'])) {
                $orderby = 't.' . $_GET['orderby'];
                $ordertype = $_GET['order_asc_desc'];
                $order= true;
            }
        }

        /* Build fake SQL query */
        $query = "SELECT " . explode('.', $finalselect)[1] . " FROM " . $from;
        if ($where != "")
            $query = $query . " WHERE " . explode('.', $where)[1];
        if ($orderby != "" and $ordertype != "")
            $query = $query . " ORDER BY " . explode('.', $orderby)[1] . " " . strtoupper($ordertype);
        /* Finally, execute SQL */
        return array(
            'result' => $this->customSQL($finalselect, $from, $where, $orderby, $order, $ordertype),
            'query' => $query
        );

    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Fire a custom SQL query.
     *
     * @param $finalselect
     * @param $from
     * @param $where
     * @param $orderby
     * @param $order
     * @param $ordertype
     * @return array with query results
     */
    private function customSQL($finalselect, $from, $where, $orderby, $order, $ordertype) {

        $qb = $this->em->getConnection()->createQueryBuilder();
        if (strlen($where) != 0 and $order == true) {
            return $qb->select($finalselect)
                ->from($from, 't')
                ->where($where)
                ->orderBy($orderby, $ordertype)
                ->execute()->fetchAll();
        } else if (strlen($where) != 0) {
            return $qb->select($finalselect)
                ->from($from, 't')
                ->where($where)
                ->execute()->fetchAll();
        } else if ($order == true) {
            return $qb->select($finalselect)
                ->from($from, 't')
                ->orderBy($orderby, $ordertype)
                ->execute()->fetchAll();
        } else {
            return $qb->select($finalselect)
                ->from($from, 't')
                ->execute()->fetchAll();
        }

    }

}