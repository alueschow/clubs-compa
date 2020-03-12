<?php

namespace App\Controller\ComparisonModule;

use App\Constants;
use App\Services\Import\ComparisonModule\ComparisonImport;
use App\Services\Import\ComparisonModule\DocumentGroupImport;
use App\Services\Import\ComparisonModule\DocumentImport;
use App\Services\Import\AbstractImport;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ImportController extends BaseController
{
    /**
     * @Route("/ComparisonModule/import", name="comparisonImport")
     *
     * @return Response
     */
    public function importAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->getActiveStudyCategory() != Constants::COMPARISON_CATEGORY_STANDALONE)
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));

        $import_data = array();
        $header = array();
        $group_import = array('data' => null, 'comment' => "", 'error' => "");
        $document_import = array('data' => null, 'comment' => "", 'error' => "");

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

                $document_validation = DocumentImport::validateDocuments();

                if (strlen($document_validation['error']) === 0) {
                    // delete tables (respecting foreign keys)
                    ComparisonImport::deleteComparisonTable();
                    DocumentImport::deleteDocumentTable();

                    $document_import = DocumentImport::importDocuments($document_validation);
                } else {
                    $document_import = $document_validation;
                }





            } else {
                // TODO: Handle file size <= 0
            }
        }

        // Render template
        return $this->render('comparison_module/import/import.html.twig',
            array(
                'result' => $import_data,
                'header' => $header,
                'doc_group_import' => $group_import,
                'doc_import' => $document_import
            )
        );

    }

}