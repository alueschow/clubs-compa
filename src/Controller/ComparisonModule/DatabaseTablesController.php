<?php

namespace App\Controller\ComparisonModule;

use App\Constants;
use App\Entity\ComparisonModule\Comparison;
use App\Entity\ComparisonModule\Configuration\DocumentGroup;
use App\Entity\ComparisonModule\Document;
use App\Repository\ComparisonModule\BaseComparisonRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DatabaseTablesController extends BaseController
{
    /**
     * @Route("/ComparisonModule/showDataOverview", name="showComparisonDataOverview")
     *
     * @return Response
     */
    public function showDataOverview()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $this->getDoctrine()->getRepository(DocumentGroup::class);
        $doc_groups_results = $repository->findBy([], ['name' => 'ASC']);

        $repository = $this->getDoctrine()->getRepository(Document::class)->findAll();
        $total_number_of_documents = count($repository);

        return $this->render('comparison_module/data/data_overview.html.twig',
            array(
                'doc_groups_results' => $doc_groups_results,
                'total_nr_of_documents' => $total_number_of_documents
            )
        );
    }


    /**
     * @Route("/ComparisonModule/showDatabaseTables/{table}", name="showComparisonDatabaseTables")
     *
     * @param string $table
     * @return Response
     */
    public function showComparisonDatabaseTablesAction($table=null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /* Query database for selected table */
        $pO = null;
        switch($table) {
            case Constants::DB_COMPARISON_MODULE_DOCUMENT:
                $pO = Document::class; break;
            default:
                $table = Constants::DB_COMPARISON_MODULE_COMPARISON;
                $pO = Comparison::class;
        }
        $results = array_slice($this->getDoctrine()->getRepository($pO)->findAll(),
            0, Constants::MAX_ENTRIES_FOR_TABLE_PREVIEW);

        $data = array(
            'results' => $results,
            'db_tables' => Constants::databaseTablesForComparisonModuleEvaluation(),
            'selected_table' => $table,
        );

        return $this->render('comparison_module/data/database_tables.html.twig', $data);
    }


    /**
     * @Route("/ComparisonModule/showCustomSQLQuery", name="showComparisonCustomSQLQuery")
     *
     * @param BaseComparisonRepository $base_repo
     * @return Response
     */
    public function showCustomSQLQueryAction(BaseComparisonRepository $base_repo)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = array(
            'sql_query' => null,
            'sql_result' => null,
            'db_tables' => Constants::databaseTablesForComparisonModuleEvaluation(),
            'nothing_selected' => false
        );

        if (isset($_GET['select'])) {
            $sql = $base_repo->executeCustomSQL();
            $data['sql_result'] = $sql['result'];
            $data['sql_query'] = $sql['query'];
        } else if (isset($_GET['table'])) {
            $data['nothing_selected'] = true;
        }

        return $this->render('comparison_module/data/custom_sql_query.html.twig', $data);
    }

}