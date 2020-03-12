<?php

namespace App\Controller\ComparisonModule;

use App\Constants;
use App\Entity\ComparisonModule\Comparison;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;


class ExportController extends BaseController
{
    /**
     * @Route("/ComparisonModule/export/{type}", name="comparisonExport")
     *
     * @param null $type
     * @return Response
     */
    public function assessmentExportAction($type=null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($type == 'database-sql') {
            return $this->dumpToSQL();
        } else if ($type == 'database-csv') {
            return $this->dumpToCSV();
        }
        // Render template
        return $this->render('comparison_module/export/export_page.html.twig');
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * @return BinaryFileResponse
     */
    private function dumpToSQL()
    {
        $filepath = getcwd() . '/../src/Resources/export_data/' . Constants::COMPARISON_MODULE_DATABASE_EXPORT_SQL_FILENAME;

        // dump database to file
        $dbname = $this->getParameter('database_name');
        $dbuser = $this->getParameter('database_user');
        $dbpassword = $this->getParameter('database_password');
        $dbhost = $this->getParameter('database_host');

        $dump = shell_exec('mysqldump --user=' . $dbuser
            . ' --password=' . $dbpassword
            . ' --host=' . $dbhost . ' '
            . $dbname . ' ComparisonModule_Comparison');

        file_put_contents($filepath, $dump);

        // download file
        $response = new BinaryFileResponse($filepath);
        $response->headers->set('Content-Type', 'text/text');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filepath));

        return $response;

    }


    /**
     * @return Response
     */
    private function dumpToCSV()
    {
        $delimiter = "\t";
        $filepath = getcwd() . '/../src/Resources/export_data/' . Constants::COMPARISON_MODULE_DATABASE_EXPORT_CSV_COMPARISON_TABLE;

        // get Assessment table as CSV
        $comparisons = $this->getDoctrine()->getRepository(Comparison::class)->findAll();
        $f = fopen($filepath, 'w');
        $fields = array('id', 'query', 'preferred_document', 'other_document', 'tester');
        fputcsv($f, $fields, $delimiter);

        /** @var Comparison $comp */
        foreach ($comparisons as $comp) {
            $lineData = array();
            $lineData[] = $comp->getId();
            $lineData[] = $comp->getQuery();
            $lineData[] = $comp->getPreferred_Document();
            $lineData[] = $comp->getOther_Document();
            $lineData[] = $comp->getTester();
            fputcsv($f, $lineData, $delimiter);
        }
        fclose($f);

        $response = new BinaryFileResponse($filepath);
        $response->headers->set('Content-Type', 'text/text');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filepath));
        return $response;
    }

}