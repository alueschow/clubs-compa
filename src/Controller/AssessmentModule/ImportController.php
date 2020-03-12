<?php

namespace App\Controller\AssessmentModule;

use App\Constants;
use App\Services\Import\AssessmentImport;
use App\Services\Import\DocumentGroupImport;
use App\Services\Import\DocumentImport;
use App\Services\Import\AbstractImport;
use App\Services\Import\DQCombinationImport;
use App\Services\Import\QueryImport;
use App\Services\Import\RunImport;
use App\Services\Import\SearchResultImport;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ImportController extends BaseController
{
    /**
     * @Route("/AssessmentModule/import", name="assessmentImport")
     *
     * @return Response
     */
    public function importAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $import_data = array();
        $header = array();
        $group_import = array('data' => null, 'comment' => "", 'error' => "");
        $document_import = array('data' => null, 'comment' => "", 'error' => "");
        $query_import = array('data' => null, 'comment' => "", 'error' => "");
        $search_result_import = array('data' => null, 'comment' => "", 'error' => "");
        $dq_combination_import = array('data' => null, 'comment' => "", 'error' => "");
        $run_import = array('data' => null, 'comment' => "", 'error' => "");

        // Handle upload
        if (isset($_POST["import"])) {
            $fileName = $_FILES["file"]["tmp_name"];
            if ($_FILES["file"]["size"] > 0) {
                $file = fopen($fileName, "r");

                // Load CSV header and data
                $header = fgetcsv($file, 0, Constants::CSV_DELIMITER);
                while (($column = fgetcsv($file, 0, Constants::CSV_DELIMITER)) !== false) {
                    $import_data[] = array_combine($header, $column);
                }

                // Start importing of data
                AbstractImport::setEntityManager($this->getDoctrine()->getManager());
                AbstractImport::setHeader($header);
                AbstractImport::setImportData($import_data);

                $group_import = DocumentGroupImport::importGroups();
                $run_import = RunImport::importRuns();

                $document_validation = DocumentImport::validateDocuments();
                $query_validation = QueryImport::validateQueries();
                $sr_validation = SearchResultImport::validateSearchResults();
                $dq_validation = DQCombinationImport::validateDQCombinations();

                if (strlen($document_validation['error']) === 0
                    && strlen($query_validation['error']) === 0
                    && strlen($sr_validation['error']) === 0) {
                    // delete tables (respecting foreign keys)
                    AssessmentImport::deleteAssessmentTable();
                    SearchResultImport::deleteSearchResultTable();
                    DQCombinationImport::deleteDQCombinationTable();
                    DocumentImport::deleteDocumentTable();
                    QueryImport::deleteQueryTable();

                    $document_import = DocumentImport::importDocuments($document_validation);
                    $query_import = QueryImport::importQueries($query_validation);
                    $search_result_import = SearchResultImport::importSearchResults($sr_validation);
                    $dq_combination_import = DQCombinationImport::importDQCombinations($dq_validation);
                } else {
                    $document_import = $document_validation;
                    $query_import = $query_validation;
                    $search_result_import = $sr_validation;
                    $dq_combination_import = $dq_validation;
                }





            } else {
                // TODO: Handle file size <= 0
            }
        }

        // Render template
        return $this->render('assessment_module/import/import.html.twig',
            array(
                'result' => $import_data,
                'header' => $header,
                'doc_group_import' => $group_import,
                'doc_import' => $document_import,
                'query_import' => $query_import,
                'search_result_import' => $search_result_import,
                'dq_combination_import' => $dq_combination_import,
                'run_import' => $run_import
            )
        );

    }

}