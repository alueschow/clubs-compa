<?php
namespace App;


class Constants
{
    /* GENERAL */
    const CSV_DELIMITER = "\t";



    /* ROLES */
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_ASSESSMENT = 'ROLE_ASSESSMENT';
    const ROLE_COMPARISON = 'ROLE_COMPARISON';
    public static function allRoles() {
        return [
            Constants::ROLE_ADMIN,
            Constants::ROLE_COMPARISON,
            Constants::ROLE_ASSESSMENT,
        ];
    }



    /* FILE NAMES */
    const ASSESSMENT_MODULE_BASIC_EXPORT_FILENAME = 'basic_assessment_statistics_export.csv';
    const ASSESSMENT_MODULE_DETAILED_EXPORT_FILENAME = 'detailed_assessment_statistics_export.csv';
    const ASSESSMENT_MODULE_DATABASE_EXPORT_SQL_FILENAME = 'assessment_dump.sql';
    const ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_FILENAME = 'assessment_csv_dump.zip';
    const ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_ASSESSMENT_TABLE = "Assessment_export.csv";
    const ASSESSMENT_MODULE_DATABASE_EXPORT_CSV_DQCOMBINATION_TABLE= "DQCombination_export.csv";

    const COMPARISON_MODULE_DATABASE_EXPORT_SQL_FILENAME = 'comparison_dump.sql';
    const COMPARISON_MODULE_DATABASE_EXPORT_CSV_COMPARISON_TABLE = "Comparison_export.csv";


    /* ASSESSMENT MODULE */
    const ASSESSMENT_MODULE = 'assessment';

    const ASSESSMENT_MODULE_BASIC_RESULTS = 'basic';
    const ASSESSMENT_MODULE_DETAILED_RESULTS = 'detailed';

    const ASSESSMENT_CATEGORY = "Assessment";
    const CLASSIFICATION_CATEGORY = "Classification";
    public static function AssessmentStudyCategories() {
        return array(
            Constants::CLASSIFICATION_CATEGORY,
            Constants::ASSESSMENT_CATEGORY
        );
    }

    const ASSESSMENT_CONFIGURATION_MODE = "Configuration";
    const ASSESSMENT_PRODUCTION_MODE = "Production";
    public static function AssessmentApplicationModes() {
        return array(
            Constants::ASSESSMENT_CONFIGURATION_MODE,
            Constants::ASSESSMENT_PRODUCTION_MODE
        );
    }

    // Name for skipped assessments
    const SKIPPED = 'skipped';

    const SKIP_REJECT_OPTION = "Reject";
    const SKIP_POSTPONE_OPTION = "Postpone";
    const SKIP_BOTH_OPTION = "Both";

    // Metrics
    const INVALID_VALUE = -1;  // Placeholder value for metrics calculation, if no metric can be calculated; must be an integer

    const R_PRECISION = "R-precision";
    const PRECISION = "Precision";
    const RECALL = "Recall";
    /* add more metrics here ... */

    public static function getMetricsNames() {
        return array(
            Constants::R_PRECISION => Constants::R_PRECISION,
            Constants::PRECISION => Constants::PRECISION,
            Constants::RECALL => Constants::RECALL,
            /* add more metrics here ... */
        );
    }



    /* COMPARISON MODULE */
    const COMPARISON_MODULE = 'comparison';
    const COMPARISON_MODULE_LIVE = 'live';
    const COMPARISON_MODULE_STANDALONE = 'standalone';

    const COMPARISON_CATEGORY_LIVE = "Live";
    const COMPARISON_CATEGORY_STANDALONE = "Standalone";
    public static function ComparisonStudyCategories() {
        return array(
            Constants::COMPARISON_CATEGORY_LIVE,
            Constants::COMPARISON_CATEGORY_STANDALONE
        );
    }

    const COMPARISON_CONFIGURATION_MODE = "Configuration";
    const COMPARISON_PRODUCTION_MODE = "Production";
    public static function ComparisonApplicationModes() {
        return array(
            Constants::COMPARISON_CONFIGURATION_MODE,
            Constants::COMPARISON_PRODUCTION_MODE
        );
    }



    /* BOTH MODULES */
    const IFRAME_MODE = "iframe";
    const DOC_INFO_MODE = "document_information";
    const IMAGE_MODE = "image";

    const GROUP_BY_QUERY = 'query';
    const GROUP_BY_DOCUMENT = 'document';
    const GROUP_BY_DOC_GROUP = 'document_group';

    const DOCUMENT_ORDER_LEFT = 'left';
    const DOCUMENT_ORDER_RIGHT = 'right';



    /* RESULTS DISPLAY OPTIONS */
    // Number of entries for table previews on "Show results" page
    const MAX_ENTRIES_FOR_TABLE_PREVIEW = 20;



    /* DATABASE TABLES */
    const DB_ASSESSMENT_MODULE_ASSESSMENT = "Assessment";
    const DB_ASSESSMENT_MODULE_QUERY = "Query";
    const DB_ASSESSMENT_MODULE_DOCUMENT = "Document";
    const DB_ASSESSMENT_MODULE_SEARCHRESULT = "SearchResult";
    const DB_ASSESSMENT_MODULE_DQCOMBINATION = "DQCombination";

    const DB_COMPARISON_MODULE_COMPARISON = "Comparison";
    const DB_COMPARISON_MODULE_DOCUMENT = "Document";

    // Database tables used in Comparison module
    public static function databaseTablesForComparisonModuleEvaluation() {
        return array(
            'Comparison' => array(
                'ID' => 'id',
                'Query' => 'query',
                'Preferred Document' => 'preferred_document',
                'Other Document' => 'other_document',
                'Tester' => 'tester'),
            'Document' => array(
                'Document ID' => 'doc_id',
                'Document Group' => 'doc_group',
                'Field 1' => 'field_1',
                'Field 2' => 'field_2',
                'Field 3' => 'field_3',
                'Field 4' => 'field_4',
                'Shown' => 'shown')
        );
    }
    // Database tables used in Assessment module
    public static function databaseTablesForAssessmentModuleEvaluation() {
        return array(
            'Assessment' => array(
                'ID' => 'id',
                'Document ID' => 'document',
                'Query ID' => 'query',
                'Rating' => 'rating',
                'Assessor' => 'assessor'),
            'Query' => array(
                'Query ID' => 'query_id',
                'Query' => 'query',
                'Topic Description' => 'description'),
            'Document' => array(
                'Document ID' => 'doc_id',
                'Document Group' => 'doc_group',
                'Field 1' => 'field_1',
                'Field 2' => 'field_2',
                'Field 3' => 'field_3',
                'Field 4' => 'field_4'),
            'SearchResult' => array(
                'ID' => 'id',
                'Document ID' => 'document',
                'Query ID' => 'query',
                'Run ID' => 'run_id',
                'Rank' => 'rank',
                'Number Found' => 'num_found'),
            'DQCombination' => array(
                'ID' => 'id',
                'Document ID' => 'document',
                'Query ID' => 'query',
                'Evaluated' => 'evaluated',
                'Skipped' => 'skipped',
                'Postponed' => 'postponed')
        );
    }

}

