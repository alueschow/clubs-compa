<?php

namespace App\Controller\AssessmentModule;

use App\Constants;
use App\Entity\AssessmentModule\Assessment;
use App\Entity\AssessmentModule\Metrics\MetricConfiguration;
use App\Entity\AssessmentModule\DQCombination;
use App\ResultsUtils;
use App\Services\AssessmentStatistics;
use App\Services\Metrics;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;


class ExportController extends BaseController
{
    /**
     * @Route("/AssessmentModule/export/{type}", name="assessmentExport")
     *
     * @param AssessmentStatistics $statistics
     * @param Metrics $metrics
     * @param null $type
     * @return Response
     */
    public function assessmentExportAction(AssessmentStatistics $statistics, Metrics $metrics, $type=null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($type == 'basic') {
            ResultsUtils::setActiveStudyCategory($this->getActiveStudyCategory());
            $data = ResultsUtils::getBasicResults($statistics);

            // Export as CSV file
            $response = $this->render('assessment_module/export/_basic_statistics_as_csv.twig', $data);
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . Constants::ASSESSMENT_MODULE_BASIC_EXPORT_FILENAME . '"');

            return $response;
        } else if ($type == 'detailed') {
            ResultsUtils::setEntityManager($this->getDoctrine()->getManager());
            ResultsUtils::setActiveStudyCategory($this->getActiveStudyCategory());
            $metrics_config = $this->getDoctrine()->getRepository(MetricConfiguration::class)->find(2);
            ResultsUtils::setMetricsConfiguration($metrics_config);
            $data = ResultsUtils::getDetailedResults($statistics, $metrics);

            // Export as CSV file
            $response = $this->render('assessment_module/export/_detailed_statistics_as_csv.twig', $data);
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . Constants::ASSESSMENT_MODULE_DETAILED_EXPORT_FILENAME . '"');

            return $response;
        } else if ($type == 'database-sql') {
            return $this->dumpToSQL();
        } else if ($type == 'database-csv') {
            return $this->dumpToCSV();
        }
        // Render template
        return $this->render('assessment_module/export/export_page.html.twig');
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
        $filepath = getcwd() . '/../src/Resources/export_data/' . Constants::ASSESSMENT_MODULE_DATABASE_EXPORT_SQL_FILENAME;

        // dump database to file
        $dbname = $this->getParameter('database_name');
        $dbuser = $this->getParameter('database_user');
        $dbpassword = $this->getParameter('database_password');
        $dbhost = $this->getParameter('database_host');

        $dump = shell_exec('mysqldump --user=' . $dbuser
            . ' --password=' . $dbpassword
            . ' --host=' . $dbhost . ' '
            . $dbname . ' AssessmentModule_Assessment AssessmentModule_DQCombination');

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
        $filepath_1 = getcwd() . '/../src/Resources/export_data/' . Constants::ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_ASSESSMENT_TABLE;
        $filepath_2 = getcwd() . '/../src/Resources/export_data/' . Constants::ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_DQCOMBINATION_TABLE;
        $zipname = dirname($filepath_1) . '/' . Constants::ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_FILENAME;

        // get Assessment table as CSV
        $assessments = $this->getDoctrine()->getRepository(Assessment::class)->findAll();
        $f = fopen($filepath_1, 'w');
        $fields = array('id', 'document', 'query', 'rating', 'assessor');
        fputcsv($f, $fields, $delimiter);

        /** @var Assessment $ass */
        foreach ($assessments as $ass) {
            $lineData = array();
            $lineData[] = $ass->getId();
            $lineData[] = $ass->getDocument()->getDoc_Id();
            $lineData[] = $ass->getQuery()->getQuery_Id();
            $lineData[] = $ass->getRating();
            $lineData[] = $ass->getAssessor();
            fputcsv($f, $lineData, $delimiter);
        }
        fclose($f);

        // get DQCombination table as CSV
        $dqcombinations = $this->getDoctrine()->getRepository(DQCombination::class)->findAll();
        $f2 = fopen($filepath_2, 'w');
        $fields = array('id', 'document', 'query', 'evaluated', 'skipped', 'postponed');
        fputcsv($f2, $fields, $delimiter);

        /** @var DQCombination $dq */
        foreach ($dqcombinations as $dq) {
            $lineData = array();
            $lineData[] = $dq->getId();
            $lineData[] = $dq->getDocument()->getDoc_Id();
            $lineData[] = $dq->getQuery()->getQuery_Id();
            $lineData[] = $dq->getEvaluated();
            $lineData[] = $dq->isSkipped() ? "1" : "0";
            $lineData[] = $dq->isPostponed() ? "1" : "0";
            fputcsv($f2, $lineData, $delimiter);
        }
        fclose($f2);

        // put both files into zip archive and return zip download
        $zip = new ZipArchive();
        file_put_contents($zipname, "");
        if ($zip->open($zipname, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$zipname>\n");
        }
        $zip->addFile($filepath_1,Constants::ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_ASSESSMENT_TABLE);
        $zip->addFile($filepath_2,Constants::ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_DQCOMBINATION_TABLE);
        $zip->close();

        // download file
        $response = new Response(file_get_contents($zipname));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . basename($zipname));

        return $response;
    }

}