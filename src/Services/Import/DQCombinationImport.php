<?php

namespace App\Services\Import;

use App\Entity\AssessmentModule\Document;
use App\Entity\AssessmentModule\DQCombination;
use App\Entity\AssessmentModule\Query;


class DQCombinationImport extends AbstractImport
{

    /**
     * Relevant columns: id, doc_id, query_id
     * @param $validation
     * @return array
     */
    public static function importDQCombinations($validation) {
        $error = $validation['error'];
        $comment = $validation['comment'];
        $success = false;

        foreach (array_keys($validation['data']) as $key) {
            $doc_id = $validation['data'][$key]['doc_id'];
            $query_id = $validation['data'][$key]['query_id'];
            $evaluated = $validation['data'][$key]['evaluated'];
            $skipped = $validation['data'][$key]['skipped'];
            $postponed = $validation['data'][$key]['postponed'];
            $skip_reason = $validation['data'][$key]['skip_reason'];
            try {
                self::$em = self::createNewEntityManager();
                self::insertDQCombinations($doc_id, $query_id,
                    $evaluated, $skipped,
                    $postponed, $skip_reason);
                $success = true;
            } catch (\Exception $e) {
                $error .= $e->getMessage();
                $success = false;
                break;
            }
        }

        $comment .= sizeOf($validation['data']) . " unique DQCombinations in total.\n";

        return array(
            'data' => $validation['data'],
            'comment' => $comment,
            'error' => $error,
            'success' => $success
        );
    }


    public static function validateDQCombinations()
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
                } else {
                    // create array key
                    $array_key = $row['doc_id'] . "$$$$$" . $row['query_id'];
                    if (!array_key_exists($array_key, $sr_import)) {
                        $sr_import[$array_key] = array();
                    } else {
                        // skip already existent document/query combinations
                        continue;
                    }
                    $sr_import[$array_key]['doc_id'] = $row['doc_id'];
                    $sr_import[$array_key]['query_id'] = $row['query_id'];
                    $sr_import[$array_key]['evaluated'] = 0;
                    $sr_import[$array_key]['skipped'] = 0;
                    $sr_import[$array_key]['postponed'] = 0;
                    $sr_import[$array_key]['skip_reason'] = "";
                }
            }
        }
        return array('data' => $sr_import, 'comment' => $comment, 'error' => $error, 'success' => false);
    }


    public static function deleteDQCombinationTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(DQCombination::class, 'dq')
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
     * @param $evaluated
     * @param $skipped
     * @param $postponed
     * @param $skip_reason
     * @throws \Exception
     */
    private static function insertDQCombinations($doc_id, $query_id, $evaluated, $skipped, $postponed, $skip_reason)
    {
        $doc = self::$em->getRepository(Document::class)->findBy(['doc_id' => $doc_id])[0];
        $query = self::$em->getRepository(Query::class)->find($query_id)[0];
        $dq = new DQCombination();
        $dq->setDocument($doc);
        $dq->setQuery($query);
        $dq->setEvaluated($evaluated);
        $dq->setSkip($skipped);
        $dq->setPostponed($postponed);
        $dq->setSkipReason($skip_reason);

        try {
            self::$em->persist($dq);
            self::$em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while inserting into database.
            Probably one or more DQCombinations already exist.
            This should not happen ... please check if the database table was correctly deleted.\n" . $e->getMessage());
        }
    }

}
