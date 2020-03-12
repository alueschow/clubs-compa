<?php

namespace App\Services\Import;

use App\Constants;
use App\Entity\AssessmentModule\Document;
use App\Entity\AssessmentModule\Query;
use App\Entity\AssessmentModule\SearchResult;


class SearchResultImport extends AbstractImport
{

    /**
     * Relevant columns: id, doc_id, query_id, run_id, rank, num_found
     * @param $validation
     * @return array
     */
    public static function importSearchResults($validation) {
        $error = $validation['error'];
        $comment = $validation['comment'];
        $success = false;

        foreach (array_keys($validation['data']) as $key) {
            $doc_id = $validation['data'][$key]['doc_id'];
            $query_id = $validation['data'][$key]['query_id'];
            $run_id = $validation['data'][$key]['run_id'];
            $rank = $validation['data'][$key]['rank'];
            $num_found = $validation['data'][$key]['num_found'];
            try {
                self::$em = self::createNewEntityManager();
                self::insertSearchResults($doc_id, $query_id, $run_id, $rank, $num_found);
                $success = true;
            } catch (\Exception $e) {
                $error .= $e->getMessage();
                $success = false;
                break;
            }
        }

        $comment .= sizeOf($validation['data']) . " SearchResults in total.\n";

        return array(
            'data' => $validation['data'],
            'comment' => $comment,
            'error' => $error,
            'success' => $success
        );
    }


    public static function validateSearchResults()
    {
        $sr_import = array();
        $comment = "";
        $error = "";

        // check if all columns in data
        if (array_search("id", self::$header) < 0 || array_search('id', self::$header) === false) {
            $error .= "Column 'id' not found.\n";
        } else if (array_search('doc_id', self::$header) < 0 || array_search('doc_id', self::$header) === false) {
            $error .= "Column 'doc_id' not found.\n";
        } else if (array_search('query_id', self::$header) < 0 || array_search('query_id', self::$header) === false) {
            $error .= "Column 'query_id' not found.\n";
        } else if (array_search('run_id', self::$header) < 0 || array_search('run_id', self::$header) === false) {
            $error .= "Column 'run_id' not found.\n";
        } else {
            // collect all documents
            foreach (self::$import_data as $row) {
                if (!isset($row['id'])) {
                    $error .= "Some entries have no ID!\n";
                    break;
                } else if (!isset($row['doc_id'])) {
                    $error .= "Document ID missing for dataset " . $row['id'] . "!\n";
                    break;
                } else if (!isset($row['query_id'])) {
                    $error .= "Query ID missing for dataset " . $row['id'] . "!\n";
                    break;
                } else if (!isset($row['run_id'])) {
                    $error .= "Run ID missing for dataset " . $row['id'] . "!\n";
                    break;
                } else {
                    // create array key
                    $array_key = $row['doc_id'] . "$$$$$" . $row['query_id'] . "$$$$$" . $row['run_id'] . "$$$$$" . $row['rank'];
                    if (!array_key_exists($array_key, $sr_import)) {
                        $sr_import[$array_key] = array();
                    } else {
                        $error .= "Data not consistent. Combination of document ID / query ID / run ID\n '" . $row['doc_id']
                            . " / " . $row['query_id'] . " / " . $row['run_id'] . "'\n appears multiple times in the data.\n";
                        break;
                    }
                    $sr_import[$array_key]['doc_id'] = $row['doc_id'];
                    $sr_import[$array_key]['query_id'] = $row['query_id'];
                    $sr_import[$array_key]['run_id'] = $row['run_id'];
                    if (!empty($row['rank'])) {
                        $sr_import[$array_key]['rank'] = $row['rank'];
                    } else {
                        $sr_import[$array_key]['rank'] = Constants::INVALID_VALUE;
                        $comment .= "Rank for dataset ID " . $row['id'] . " not set. Set to value " . Constants::INVALID_VALUE . ".\n";
                    }
                    if (!empty($row['num_found'])) {
                        $sr_import[$array_key]['num_found'] = $row['num_found'];
                    } else {
                        $sr_import[$array_key]['num_found'] = Constants::INVALID_VALUE;
                        $comment .= "Num Found for dataset ID " . $row['id'] . " not set. Set to value " . Constants::INVALID_VALUE . ".\n";
                    }
                }
            }
        }
        return array('data' => $sr_import, 'comment' => $comment, 'error' => $error, 'success' => false);
    }


    public static function deleteSearchResultTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(SearchResult::class, 'sr')
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
     * @param $doc_id
     * @param $query_id
     * @param $run_id
     * @param $rank
     * @param $num_found
     * @throws \Exception
     */
    private static function insertSearchResults($doc_id, $query_id, $run_id, $rank, $num_found)
    {
        $doc = self::$em->getRepository(Document::class)->findBy(['doc_id' => $doc_id])[0];
        $query = self::$em->getRepository(Query::class)->find($query_id)[0];
        $sr = new SearchResult();
        $sr->setDocument($doc);
        $sr->setQuery($query);
        $sr->setRun_Id($run_id);
        $sr->setRank($rank);
        $sr->setNum_Found($num_found);

        try {
            self::$em->persist($sr);
            self::$em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while inserting into database.
            Probably one or more SearchResults already exist.
            This should not happen ... please check if the database table was correctly deleted.\n" . $e->getMessage());
        }
    }

}
