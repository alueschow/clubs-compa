<?php

namespace App\Controller\AssessmentModule;

use App\Constants;
use App\Entity\AssessmentModule\Assessment;
use App\Entity\AssessmentModule\Configuration\DocumentGroup;
use App\Entity\AssessmentModule\Document;
use App\Entity\AssessmentModule\DQCombination;
use App\Entity\AssessmentModule\Query;
use App\Entity\AssessmentModule\Run;
use App\Entity\AssessmentModule\SearchResult;
use App\Repository\AssessmentModule\BaseAssessmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DatabaseTablesController extends BaseController
{
    /**
     * @Route("/AssessmentModule/showDataOverview", name="showAssessmentDataOverview")
     *
     * @return Response
     */
    public function showDataOverview()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $this->getDoctrine()->getRepository(DocumentGroup::class);
        $doc_groups_results = $repository->findBy([], ['name' => 'ASC']);

        $repository = $this->getDoctrine()->getRepository(Run::class);
        $run_results = $repository->findBy([], ['name' => 'ASC']);

        $repository = $this->getDoctrine()->getRepository(Query::class)->findAll();
        $total_number_of_queries = count($repository);

        $repository = $this->getDoctrine()->getRepository(Document::class)->findAll();
        $total_number_of_documents = count($repository);

        $repository = $this->getDoctrine()->getRepository(DQCombination::class)->findAll();
        $total_number_of_dqcombinations = count($repository);

        $repository = $this->getDoctrine()->getRepository(SearchResult::class)->findAll();
        $total_number_of_search_results = count($repository);

        return $this->render('assessment_module/data/data_overview.html.twig',
            array(
                'doc_groups_results' => $doc_groups_results,
                'run_results' => $run_results,
                'total_nr_of_queries' => $total_number_of_queries,
                'total_nr_of_documents' => $total_number_of_documents,
                'total_nr_of_dqcombinations' => $total_number_of_dqcombinations,
                'total_nr_of_search_results' => $total_number_of_search_results
            )
        );
    }


    /**
     * @Route("/AssessmentModule/showDatabaseTables/{table}", name="showAssessmentDatabaseTables")
     *
     * @param string $table
     * @return Response
     */
    public function showAssessmentDatabaseTablesAction($table=null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /* Query database for selected table */
        $pO = null;
        switch($table) {
            case Constants::DB_ASSESSMENT_MODULE_SEARCHRESULT:
                $pO = SearchResult::class; break;
            case Constants::DB_ASSESSMENT_MODULE_DOCUMENT:
                $pO = Document::class; break;
            case Constants::DB_ASSESSMENT_MODULE_QUERY:
                $pO = Query::class; break;
            case Constants::DB_ASSESSMENT_MODULE_DQCOMBINATION:
                $pO = DQCombination::class; break;
            default:
                $table = Constants::DB_ASSESSMENT_MODULE_ASSESSMENT;
                $pO = Assessment::class;
        }
        $results = array_slice($this->getDoctrine()->getRepository($pO)->findAll(),
            0, Constants::MAX_ENTRIES_FOR_TABLE_PREVIEW);

        $data = array(
            'results' => $results,
            'db_tables' => Constants::databaseTablesForAssessmentModuleEvaluation(),
            'selected_table' => $table,
        );

        return $this->render('assessment_module/data/database_tables.html.twig', $data);
    }


    /**
     * @Route("/AssessmentModule/showCustomSQLQuery", name="showAssessmentCustomSQLQuery")
     *
     * @param BaseAssessmentRepository $base_repo
     * @return Response
     */
    public function showCustomSQLQueryAction(BaseAssessmentRepository $base_repo)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = array(
            'sql_query' => null,
            'sql_result' => null,
            'db_tables' => Constants::databaseTablesForAssessmentModuleEvaluation(),
            'nothing_selected' => false
        );

        if (isset($_GET['select'])) {
            $sql = $base_repo->executeCustomSQL();
            $data['sql_result'] = $sql['result'];
            $data['sql_query'] = $sql['query'];
        } else if (isset($_GET['table'])) {
            $data['nothing_selected'] = true;
        }

        return $this->render('assessment_module/data/custom_sql_query.html.twig', $data);
    }

}