<?php

namespace App\Services\Import;

use App\Entity\AssessmentModule\Run;


class RunImport extends AbstractImport
{

    /**
     * Relevant columns: run_id
     */
    public static function importRuns() {
        $validation = self::validateRuns();
        $error = $validation['error'];

        if (strlen($validation['comment']) == 0) {
            self::deleteRunTable();
            foreach (array_keys($validation['data']) as $key) {
                $run = $key;
                $short_name = $validation['data'][$key]['short_name'][0];
                $description = $validation['data'][$key]['description'][0];
                try {
                    self::$em = self::createNewEntityManager();
                    self::insertRuns($run, $short_name, $description);
                } catch (\Exception $e) {
                    $error .= $e->getMessage();
                    break;
                }
            }
        }

        return array(
            'data' => $validation['data'],
            'comment' => $validation['comment'],
            'name_duplicates' => $validation['name_duplicates'],
            'description_duplicates' => $validation['description_duplicates'],
            'error' => $error
        );
    }


    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    private static function validateRuns()
    {
        $run_import = array();
        $name_duplicates = array();
        $description_duplicates = array();
        $comment = "";
        $error = "";
        $run_short_name_errors = false;
        $run_description_errors = false;

        // check if all columns in data
        if (array_search('run_id', self::$header) < 0 || array_search('run_id', self::$header) === false) {
            $comment .= "Column 'run_id' not found.\n";
        } else if (array_search('run_id_short', self::$header) < 0 || array_search('run_id_short', self::$header) === false) {
            $comment .= "Column 'run_id_short' not found.\n";
        } else {
            // collect all runs
            foreach (self::$import_data as $row) {
                $run_import[] = $row['run_id'];
            }
            if (empty($run_import)) {
                $comment = "No run IDs found, check the data!\n";
            } else {
                // get unique run names and set their short names and description to empty array
                $run_import = array_flip(array_unique($run_import));
                $run_import = array_map(function () {
                    return array('short_name' => array(), 'description' => array());
                }, $run_import);

                // get short names and descriptions
                foreach (self::$import_data as $row) {
                    $val = self::getValues($run_import, $row, 'short_name', 'run_id_short');
                    $run_import[$row['run_id']]['short_name'] = $val['values'];
                    $run_short_name_errors = $val['error'];

                    $val = self::getValues($run_import, $row, 'description', 'run_description');
                    $run_import[$row['run_id']]['description'] = $val['values'];
                    $run_description_errors = $val['error'];
                }

                // check if each run has a different short name and description
                $tmp = self::checkForDuplicates($run_import, 'short_name');
                $name_duplicates = $tmp['duplicates'];
                if (!$run_short_name_errors) $run_short_name_errors = $tmp['error'];
                $tmp = self::checkForDuplicates($run_import, 'description');
                $description_duplicates = $tmp['duplicates'];
                if (!$run_description_errors) $run_description_errors = $tmp['error'];

                // error messages
                if ($run_short_name_errors)
                    $comment .= "Error, please check the data! Possible reasons:
                    - some run_short_id cells are empty, or
                    - more than one short name per run, or
                    - same short name used for different runs.\n";

                if ($run_description_errors)
                    $comment .= "Error, please check the data! Possible reasons:
                    - some run_description cells are empty, or
                    - more than one description per run, or
                    - same description used for different runs.";
            }
        }
        return array('data' => $run_import, 'comment' => $comment, 'name_duplicates' => $name_duplicates,
            'description_duplicates' => $description_duplicates,'error' => $error);
    }


    private static function getValues($run_import, $row, $attribute, $column) {
        $run_attrib = $run_import[$row['run_id']][$attribute];
        $error = false;

        $current_value = $row[$column];
        // check if attribute field is empty
        if (empty($current_value)) {
            $run_attrib[] = "(empty field)";
            $error = true;
        } else {
            $run_attrib[] = $current_value;
        }

        // remove duplicates and check for uniqueness
        $run_attrib = array_unique($run_attrib);
        if (sizeOf($run_attrib) > 1) $error = true;

        return array('values' => $run_attrib, 'error' => $error);
    }


    private static function checkForDuplicates($run_import, $attribute) {
        $error = false;
        $duplicates = array();

        foreach (array_keys($run_import) as $r) {
            foreach ($run_import[$r][$attribute] as $a) {
                foreach (array_keys($run_import) as $r2) {
                    if ($r != $r2) {
                        foreach ($run_import[$r2][$attribute] as $a2) {
                            if ($a == $a2) {
                                $duplicates[] = $a;
                            }
                        }
                    }
                }
            }
        }
        $duplicates = array_unique($duplicates);
        if (sizeOf($duplicates) > 0) $error = true;

        return array('duplicates' => $duplicates, 'error' => $error);
    }


    private static function deleteRunTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(Run::class, 'r')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param $name
     * @param $short_name
     * @param $description
     * @throws \Exception
     */
    private static function insertRuns($name, $short_name, $description="")
    {
        $run = new Run();
        $run->setName($name);
        $run->setShortName($short_name);
        $run->setDescription($description);
        $run->setActive(true);

        try {
            self::$em->persist($run);
            self::$em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while inserting into database.
            Probably one or more runs already exist.
            This should not happen ... please check if the database table was correctly deleted.\n" . $e->getMessage());
        }
    }

}
