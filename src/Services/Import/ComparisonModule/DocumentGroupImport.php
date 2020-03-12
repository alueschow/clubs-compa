<?php

namespace App\Services\Import\ComparisonModule;

use App\Entity\ComparisonModule\Configuration\DocumentGroup;
use App\Services\Import\AbstractImport;
use Exception;


class DocumentGroupImport extends AbstractImport
{
    /**
     * Check import for completeness and consistency of document groups.
     * After that, delete table and insert new document groups.
     * Relevant columns: doc_group
     */
    public static function importGroups() {
        $validation = self::validateGroups();
        $error = $validation['error'];

        if (strlen($validation['comment']) == 0) {
            self::deleteDocumentGroupTable();
            foreach (array_keys($validation['data']) as $key) {
                $name = $key;
                try {
                    self::$em = self::createNewEntityManager();
                    self::insertDocumentGroups($name);
                } catch (Exception $e) {
                    $error .= $e->getMessage();
                    break;
                }
            }
        }

        return array(
            'data' => $validation['data'],
            'comment' => $validation['comment'],
            'duplicates' => $validation['duplicates'],
            'error' => $error
        );
    }


    
    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    private static function validateGroups()
    {
        $group_import = array();
        $duplicates = array();
        $comment = "";
        $error = "";

        // check if all columns in data
        if (array_search('doc_group', self::$header) < 0 || array_search('doc_group', self::$header) === false) {
            $comment .= "Column 'doc_group' not found.\n";
        } else {
            // collect all groups
            foreach (self::$import_data as $row) {
                $group_import[] = $row['doc_group'];
            }
            if (empty($group_import)) {
                $comment = "No document groups found, check the data!\n";
            } else {
                // get unique groups
                $group_import = array_flip(array_unique($group_import));
//                $group_import = array_map(function () {
//                    return array();
//                }, $group_import);
            }
        }
        return array('data' => $group_import, 'comment' => $comment, 'duplicates' => $duplicates, 'error' => $error);
    }


    /**
     * Delete database table.
     */
    private static function deleteDocumentGroupTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(DocumentGroup::class, 'dg')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param $name
     * @throws Exception
     */
    private static function insertDocumentGroups($name)
    {
        $group = new DocumentGroup();
        $group->setName($name);
        $group->setNrOfMaxEvaluations(1);

        try {
            self::$em->persist($group);
            self::$em->flush();
        } catch (Exception $ew) {
            throw new Exception("Error while inserting into database.
            Probably one or more document groups already exist.
            This should not happen ... please check if the database table was correctly deleted.");
        }
    }

}
