<?php

namespace App\Services\Import;

use App\Entity\AssessmentModule\Query;


class QueryImport extends AbstractImport
{

    /**
     * Relevant columns: query_id, query, query_description
     * @param $validation
     * @return array
     */
    public static function importQueries($validation) {
        $error = $validation['error'];
        $comment = $validation['comment'];
        $success = false;

        foreach (array_keys($validation['data']) as $key) {
            $query_id = $key;
            $query = $validation['data'][$key]['query'];
            $description = $validation['data'][$key]['query_description'];
            try {
                self::$em = self::createNewEntityManager();
                self::insertQueries($query_id, $query, $description);
                $success = true;
            } catch (\Exception $e) {
                $error .= $e->getMessage();
                $success = false;
                break;
            }
        }

        $comment .= sizeOf($validation['data']) . " Queries in total.\n";

        return array(
            'data' => $validation['data'],
            'comment' => $comment,
            'error' => $error,
            'success' => $success
        );
    }


    public static function validateQueries()
    {
        $q_import = array();
        $comment = "";
        $error = "";

        // check if all columns in data
        if (array_search('query_id', self::$header) < 0 || array_search('query_id', self::$header) === false) {
            $error .= "Column 'query_id' not found.\n";
        } else if (array_search('query', self::$header) < 0 || array_search('query', self::$header) === false) {
            $error .= "Column 'query' not found.\n";
        } else if (array_search('query_description', self::$header) < 0 || array_search('query_description', self::$header) === false) {
            $error .= "Column 'query_description' not found.\n";
        } else {
            // collect all queries
            foreach (self::$import_data as $row) {
                if (empty($row['query_id'])) {
                    $error .= "Some entries have no ID!\n";
                } else {
                    if (!array_key_exists($row['query_id'], $q_import))
                        $q_import[$row['query_id']] = array();
                    !empty($row['query'])
                        ? $q_import[$row['query_id']]['query'] = $row['query']
                        : $error .= "No query text for query ID " . $row['query_id'] . " found!\n";
                    !empty($row['query_description'])
                        ? $q_import[$row['query_id']]['query_description'] = $row['query_description']
                        : $error .= "Topic Description missing for query ID " . $row['query_id'] . "\n";
                }
            }
        }
        return array('data' => $q_import, 'comment' => $comment, 'error' => $error, 'success' => false);
    }


    public static function deleteQueryTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(Query::class, 'd')
            ->getQuery()
            ->getResult();
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * @param $query_id
     * @param $query
     * @param $description
     * @throws \Exception
     */
    private static function insertQueries($query_id, $query, $description)
    {
        $q = new Query();
        $q->setQuery_Id($query_id);
        $q->setQuery($query);
        $q->setDescription($description);

        try {
            self::$em->persist($q);
            self::$em->flush();
        } catch (\Exception $ew) {
            throw new \Exception("Error while inserting into database.
            Probably one or more queries already exist.
            This should not happen ... please check if the database table was correctly deleted.");
        }
    }

}
