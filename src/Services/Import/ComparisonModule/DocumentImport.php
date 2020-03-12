<?php

namespace App\Services\Import\ComparisonModule;

use App\Entity\ComparisonModule\Document;
use App\Services\Import\AbstractImport;
use Exception;


class DocumentImport extends AbstractImport
{

    /**
     * Relevant columns: doc_id, doc_group, field_1 (optional), field_2 (optional), field_3 (optional), field_4 (optional)
     * @param $validation
     * @return array
     */
    public static function importDocuments($validation) {
        $error = $validation['error'];
        $comment = $validation['comment'];
        $success = false;

        foreach (array_keys($validation['data']) as $key) {
            $doc_id = $key;
            $group = $validation['data'][$key]['doc_group'];
            $field_1 = $validation['data'][$key]['field_1'];
            $field_2 = $validation['data'][$key]['field_2'];
            $field_3 = $validation['data'][$key]['field_3'];
            $field_4 = $validation['data'][$key]['field_4'];
            $shown = $validation['data'][$key]['shown'];
            try {
                self::$em = self::createNewEntityManager();
                self::insertDocuments($doc_id, $group, $field_1, $field_2, $field_3, $field_4, $shown);
                $success = true;
            } catch (Exception $e) {
                $error .= $e->getMessage();
                $success = false;
                break;
            }
        }

        $comment .= sizeOf($validation['data']) . " Documents in total.\n";

        return array(
            'data' => $validation['data'],
            'comment' => $comment,
            'error' => $error,
            'success' => $success
        );
    }


    public static function validateDocuments()
    {
        $doc_import = array();
        $comment = "";
        $error = "";

        // check if all columns in data
        if (array_search('doc_id', self::$header) < 0 || array_search('doc_id', self::$header) === false) {
            $error .= "Column 'doc_id' not found.\n";
        } else if (array_search('doc_group', self::$header) < 0 || array_search('doc_group', self::$header) === false) {
            $error .= "Column 'doc_group' not found.\n";
        } else {
            // collect all documents
            foreach (self::$import_data as $row) {
                if (empty($row['doc_id'])) {
                    $error .= "Some entries have no ID!\n";
                } else {
                    !array_key_exists($row['doc_id'], $doc_import)
                        ? $doc_import[$row['doc_id']] = array()
                        : $comment .= "Document ID " . $row['doc_id' ]. " appears multiple times in the data.\n";

                    $check = self::checkForDifferingValues($doc_import, $row, $comment, $error, "doc_group", true);
                    $doc_import[$row['doc_id']]["doc_group"] = $check['attribute'];
                    $error = $check['error'];

                    foreach (["field_1", "field_2", "field_3", "field_4"] as $attribute) {
                        $check = self::checkForDifferingValues($doc_import, $row, $comment, $error, $attribute);
                        $doc_import[$row['doc_id']][$attribute] = $check['attribute'];
                        $comment = $check['comment'];
                        $error = $check['error'];
                    }
                    $doc_import[$row['doc_id']]["shown"] = 0;
                }
            }
        }
        return array('data' => $doc_import, 'comment' => $comment, 'error' => $error, 'success' => false);
    }


    public static function deleteDocumentTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(Document::class, 'd')
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
     * @param $group
     * @param $field_1
     * @param $field_2
     * @param $field_3
     * @param $field_4
     * @throws Exception
     */
    private static function insertDocuments($doc_id, $group, $field_1, $field_2, $field_3, $field_4, $shown)
    {
        $doc = new Document();
        $doc->setDoc_Id($doc_id);
        $doc->setDoc_Group($group);
        $doc->setField_1($field_1);
        $doc->setField_2($field_2);
        $doc->setField_3($field_3);
        $doc->setField_4($field_4);
        $doc->setShown($shown);

        try {
            self::$em->persist($doc);
            self::$em->flush();
        } catch (Exception $ew) {
            throw new Exception("Error while inserting into database.
            Probably one or more documents already exist.
            This should not happen ... please check if the database table was correctly deleted.");
        }
    }


    /**
     * Check if new attribute value for a document is the same as for previous documents with the same ID
     * @param $doc_import
     * @param $row
     * @param $comment
     * @param $error
     * @param $attribute
     * @param bool $required Specifies if it is a required attribute
     * @return array
     */
    private static function checkForDifferingValues($doc_import, $row, $comment, $error, $attribute, $required=false) {
        $id = $row['doc_id'];
        $old_value_set = isset($doc_import[$id][$attribute]);
        $new_value = $row[$attribute];

        // check if field is set
        if (!empty($new_value)) {
            // check for already existent value
            if ($old_value_set) {
                $old_value = $doc_import[$id][$attribute];
                if (strlen($old_value) > 0) {
                    // check values for similarity
                    if ($old_value !== $new_value)
                        $error .= "Document ID " . $id . " has different values in '" . $attribute . "'' field!\n";
                }
                // value does not exist yet
            } else {
                $doc_import[$id][$attribute] = $new_value;
            }
        // if field not set
        } else {
            // attribute required or not
            if ($required) {
                $error .= "'" . $attribute . "' missing for document ID " . $id . "\n";
            } else {
                // check if existent value is not the empty string
                if ($old_value_set) {
                    $old_value = $doc_import[$id][$attribute];
                    if (strlen($old_value) > 0)
                        $error .= "Document ID " . $id . " has different values in '" . $attribute . "'' field!\n";
                } else {
                    // set value to empty string
                    $doc_import[$id][$attribute] = "";
                    $comment .= $attribute . " for document ID " . $id . " not set.\n";
                }
            }
        }

        return array(
            'attribute' => $doc_import[$id][$attribute],
            'error' => $error,
            'comment' => $comment
        );
    }

}
