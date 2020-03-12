<?php

namespace App\Controller\AssessmentModule;

use App\Constants;
use App\DatabaseUtils;
use App\Entity\AssessmentModule\Assessment;
use App\Entity\AssessmentModule\Configuration\RatingOption;
use App\Entity\AssessmentModule\DQCombination;
use App\Entity\AssessmentModule\SearchResult;
use App\Entity\Debug;
use App\Entity\User;
use App\Form\AssessmentModule\AssessmentType;
use App\Progress;
use App\Services\AssessmentStatistics;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;


class AssessmentController extends BaseController
{
    /**
     * @Route("/assessment", name="assessment")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param AssessmentStatistics $statistics
     * @return Response
     */
    public function assessmentAction(Request $request, SessionInterface $session, AssessmentStatistics $statistics)
    {
        $this->denyAccessUnlessGranted('ROLE_ASSESSMENT');

        if ($this->getActiveStudyCategory() !== Constants::ASSESSMENT_CATEGORY)
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));
        if ($this->getActiveMode() !== Constants::ASSESSMENT_PRODUCTION_MODE && $this->getUser()->getRoles()[0] !== Constants::ROLE_ADMIN)
            return $this->render('messages.html.twig', array('message' => 'no_access'));

        // handle get parameters
        $get_params = $this->handleGETParams($request);
        if ($get_params['database_error']) return $this->render('messages.html.twig', array('message' => 'database_error'));
        if ($get_params['skipped'] or $get_params['postponed']) return $this->redirectToRoute('assessment');

        // load data
        $dq = $this->loadAssessmentSets($session);
        if (is_null($dq)) {
            // no DQCombination left -> assessment finished
            return $this->render('messages.html.twig',
                array('message' => 'success', 'module' => Constants::ASSESSMENT_MODULE)
            );
        }

        // create form and handle form data
        $form = $this->createForm(AssessmentType::class, null,array(
            'rating_levels' => $this->getRatingOptions(),
            'rating_heading' => $this->getRatingHeading())
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tmp = $this->handleFormData($form, $dq);
            // $tmp = null means -> no problems with saving data, thus load next assessment
            if (is_null($tmp)) return $this->redirectToRoute('assessment');
        }

        // render template
        $render_array = $this->createArrayForRendering($dq, $this->getDocumentData($dq));
        $render_array_2 = array(
            'form' => $form->createView(),
            'debug_obj' => $this->createDebugObject($dq),
            'progress' => $this->getProgress($statistics)
        );
        $render_array = array_merge($render_array, $render_array_2);

        return $this->render('assessment_module/assessment.html.twig', $render_array);
    }



    /**
     * ***********************************
     * *********** PRIVATE ***************
     * ********** FUNCTIONS **************
     * ***********************************
     */

    /**
     * Handle get parameters.
     *
     * @param $request
     * @return array
     */
    private function handleGETParams($request) {
        $skipped = false;
        $postponed = false;
        $database_error = false;

        if (isset($_GET['skip_id'])) {
            try {
                $this->saveSkip($_GET['skip_id'], $request->request->get('skipreason'));
                $skipped = true;
            } catch (\Exception $e) {
                $database_error = true;
            }
        } else if (isset($_GET['postpone_id'])) {
            try {
                $this->savePostpone($_GET['postpone_id']);
                $postponed = true;
            } catch (\Exception $e) {
                $database_error = true;
            }
        }

        return array('skipped' => $skipped, 'postponed' => $postponed, 'database_error' => $database_error);
    }


    /**
     * @param $dq_id
     * @param $reason
     * @throws Exception
     */
    private function saveSkip($dq_id, $reason)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $current_dq DQCombination */
        $current_dq = $em->getRepository(DQCombination::class)->find($dq_id)[0];
        $current_dq->setSkip(1);
        // add skipping comment
        if ($this->getSkippingComment()) {
            $old_reason = $current_dq->getSkipReason();
            $current_dq->setSkipReason($old_reason . $reason . " (user: " . $this->getUser()->getUsername() . ") /// ");
        }
        try {
            $em->persist($current_dq);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while inserting into database");
        }
    }


    /**
     * @param $dq_id
     * @throws Exception
     */
    private function savePostpone($dq_id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $current_dq DQCombination */
        $current_dq = $em->getRepository(DQCombination::class)->find($dq_id)[0];
        $current_dq->setPostponed(1);
        try {
            $em->persist($current_dq);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Error while inserting into database");
        }
    }


    /**
     * Used in Assessment module.
     * Load and order assessment sets from the database that are
     * (a) in the users document groups,
     * (b) not already assessed often enough,
     * (c) not already assessed by this user.
     * Also define the Debug object, if necessary.
     *
     * @param SessionInterface $session
     * @return DQCombination
     */
    private function loadAssessmentSets(SessionInterface $session)
    {
        // get needed data
        $data = $this->getRelevantData();
        $without_postponed = $data['without_postponed'];
        $only_postponed = $data['only_postponed'];

        // order sets
        if ($this->getGroupBy()) {
            $ordered = $this->orderData($without_postponed, $only_postponed, $this->getGroupByCategory());
            $without_postponed = $ordered['without_postponed'];
            $only_postponed = $ordered['only_postponed'];
            // combine arrays; use DQCombinations without postponed first, then other DQCombinations
            $subset = array_merge($without_postponed, $only_postponed);
        } else {
            // combine arrays; use DQCombinations without postponed first, then other DQCombinations
            $subset = array_merge($without_postponed, $only_postponed);
            // no grouping desired -> shuffle
            shuffle($subset);
        }

        // if subset is empty, all searches for this assessor were evaluated, nothing to return
        if (empty($subset)) {
            return null;
        } else {
            // set Debug object information
            if ($this->getDebugModeStatus()) {
                $count_wo_pp = count($without_postponed);
                $count_only_pp = count($only_postponed);
                $count_subset = count($subset);
                // put debug object in session
                $debug_obj = Debug::createWithCounts(
                    $count_wo_pp,
                    $count_only_pp,
                    $count_subset
                );
                $session->set('debug_obj', $debug_obj);
            };
            // pass first element of $subset to retrievalAssessment route
            return $subset[0];
        }
    }


    /**
     * Get DQCombinations that
     * (a) have a document in one of the users document groups,
     * (b) were not assessed often enough,
     * (c) were not assessed by this user yet.
     *
     * @return array
     */
    private function getRelevantData()
    {
        DatabaseUtils::setEntityManager($this->getDoctrine()->getManager());
        $assessor = $this->getUser()->getUsername();
        $repo = $this->getDoctrine()->getRepository(DQCombination::class);

        // arrays that hold all relevant retrieval sets
        $without_postponed = $only_postponed = array();
        // iterate over user document groups
        foreach ($this->getUser()->getGroups() as $group) {
            $nr_evaluations = DatabaseUtils::getNrOfMaxEvaluations($group);
            // distinguish "normal" retrieval sets and those that were postponed
            $not_postponed = $repo->findUnratedDQCombinations($group, $assessor, $nr_evaluations, false);
            $postponed = $repo->findUnratedDQCombinations($group, $assessor, $nr_evaluations, true);
            // add to array
            $without_postponed = array_merge($without_postponed, $not_postponed);
            $only_postponed = array_merge($only_postponed, $postponed);
        }
        return array('without_postponed' => $without_postponed, 'only_postponed' => $only_postponed);
    }


    /**
     * Order data ascending.
     *
     * @param $without_postponed
     * @param $only_postponed
     * @param $by
     * @return array
     */
    private function orderData($without_postponed, $only_postponed, $by='query')
    {
        $without_postponed_ordered = array();
        $only_postponed_ordered = array();

        /**
         * @var int $key
         * @var DQCombination $row
         */
        foreach ($without_postponed as $key => $row) {
            if ($by == 'query') {
                $value = $row->getQuery()->getQuery_Id();
            } elseif ($by == 'document') {
                $value = $row->getDocument()->getDoc_Id();
            } elseif ($by == 'document_group') {
                $value = $row->getDocument()->getDoc_Group();
            } else {
                $value = $row->getQuery()->getQuery_Id();
            }
            $without_postponed_ordered[$key] = $value;
        }

        foreach ($only_postponed as $key => $row) {
            if ($by == 'query') {
                $value = $row->getQuery()->getQuery_Id();
            } elseif ($by == 'document') {
                $value = $row->getDocument()->getDoc_Id();
            } elseif ($by == 'document_group') {
                $value = $row->getDocument()->getDoc_Group();
            } else {
                $value = $row->getQuery()->getQuery_Id();
            }
            $only_postponed_ordered[$key] = $value;
        }

        array_multisort($without_postponed_ordered, SORT_ASC, $without_postponed);
        array_multisort($only_postponed_ordered, SORT_ASC, $only_postponed);

        return array('without_postponed' => $without_postponed, 'only_postponed' => $only_postponed);
    }


    /**
     * Create the debugging object.
     *
     * @param DQCombination $dq
     * @return Debug
     */
    private function createDebugObject($dq) {
        $dq_repo = $this->getDoctrine()->getRepository(DQCombination::class);
        $sr_repo = $this->getDoctrine()->getRepository(SearchResult::class);

        $debug_obj = new Debug();

        if ($this->getDebugModeStatus()) {
            // Get debug object from session
            $debug_obj = $this->get('session')->get('debug_obj');
            $debug_obj->add('ranks', $sr_repo->findRanks($dq->getId()));
            $debug_obj->add('run_ids', $sr_repo->findRunIds($dq->getId()));
            $debug_obj->add('num_founds', $sr_repo->findNumFound($dq->getId()));
            $debug_obj->add('evaluated', $dq_repo->findNrOfEvaluations($dq->getId())[0]['evaluated']);
            $debug_obj->add('skips', $dq_repo->findSkips('skips')[0]['col_skips']);
        }

        return $debug_obj;
    }


    /**
     * Get information about the document and the presentation.
     *
     * @param DQCombination $dq
     * @return array
     */
    private function getDocumentData($dq) {
        $doc_id = $dq->getDocument()->getDoc_Id();
        $doc_group = $dq->getDocument()->getDoc_Group();
        $presentation_fields = $this->fillPresentationFields($dq);
        $frame = $this->getAssessmentURL(). $doc_id;
        $img = $this->getAssessmentURL(). $doc_id;

        return array(
            'doc_id' => $doc_id,
            'doc_group' => $doc_group,
            'presentation_fields' => $presentation_fields,
            'frame' => $frame,
            'img' => $img
        );
    }


    /**
     * Get number of presentation fields, their names and contents and put them in an array.
     *
     * @param DQCombination $dq
     * @return array
     */
    private function fillPresentationFields($dq) {
        $presentation_fields = array();

        if ($this->getPresentationFields() >= 1) {
            !is_null($this->getPresentationFieldName1())
                ? $presentation_fields[$this->getPresentationFieldName1()] = html_entity_decode($dq->getDocument()->getField_1())
                : $presentation_fields[1] = html_entity_decode($dq->getDocument()->getField_1());
        }
        if ($this->getPresentationFields() >= 2) {
            !is_null($this->getPresentationFieldName2())
                ? $presentation_fields[$this->getPresentationFieldName2()] = html_entity_decode($dq->getDocument()->getField_2())
                : $presentation_fields[2] = html_entity_decode($dq->getDocument()->getField_2());
        }
        if ($this->getPresentationFields() >= 3) {
            !is_null($this->getPresentationFieldName3())
                ? $presentation_fields[$this->getPresentationFieldName3()] = html_entity_decode($dq->getDocument()->getField_3())
                : $presentation_fields[3] = html_entity_decode($dq->getDocument()->getField_3());
        }
        if ($this->getPresentationFields() >= 4) {
            !is_null($this->getPresentationFieldName4())
                ? $presentation_fields[$this->getPresentationFieldName4()] = html_entity_decode($dq->getDocument()->getField_4())
                : $presentation_fields[4] = html_entity_decode($dq->getDocument()->getField_4());
        }

        return $presentation_fields;
    }


    /**
     * Get total progress and progress for current user.
     * @param AssessmentStatistics $statistics
     * @return array
     */
    private function getProgress(AssessmentStatistics $statistics) {
        $progress_user = $progress_all = null;

        if ($this->getUserProgressBar()) {
            Progress::setEntityManager($this->getDoctrine()->getManager());
            $progress_user = Progress::getUserProgress($this->getUser()->getUsername(), $this->getUser()->getGroups());
        }
        if ($this->getTotalProgressBar()) {
            $stats = $statistics->getBasicStatistics();
            $progress_all = $stats['assessment_prop'];
        }

        return array('all' => $progress_all, 'user' => $progress_user);
    }


    /**
     * Get display names of rating options.
     */
    private function getRatingOptions() {
        $rl_repo = $this->getDoctrine()->getRepository(RatingOption::class);
        $option = $rl_repo->findBy(array(),['priority' => 'DESC']);

        $rating_options = array();
        foreach ($option as $o) {
            $rating_options[(string)$o->getName()] = $o->getName();
        }

        return $rating_options;
    }


    /**
     * Handle form submission.
     *
     * @param FormInterface $form
     * @param DQCombination $dq
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    private function handleFormData($form, $dq) {
        $rating = $form->getData()->getRating();

        if ($rating == 'Cancel') {
            return $this->render('messages.html.twig', array('message' => 'pause_assessment'));
        } else {
            // Save assessment in database and load next query
            try {
                $this->saveAssessment($dq, $rating, $this->getUser());
            } catch (\Exception $e) {
                return $this->render('messages.html.twig', array('message' => 'database_error'));
            }
            return null;
        }
    }


    /**
     * @ParamConverter("dq", class="App:DQCombination")
     *
     * @param DQCombination $dq
     * @param $rating
     * @param $assessor
     * @throws \Exception
     */
    private function saveAssessment(DQCombination $dq, $rating, $assessor)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $dq->getQuery();
        $doc = $dq->getDocument();
        $dq_id = $dq->getId();

        /* Save assessment to database */
        $assessment = new Assessment();
        $assessment->setDocument($doc);
        $assessment->setQuery($query);
        $assessment->setRating($rating);
        /** @var User $assessor */
        $assessment->setAssessor($assessor->getUsername());

        // Prevent the application from showing an error when the data set
        // can not be ingested into the database
        // (happens e.g. when double-clicking on a submit button)
        try {
            $em->persist($assessment);
            $em->flush();
        } catch (Exception $e) {
            throw new \Exception("Error while inserting into database");
        }

        // update SearchResult database
        /** @var DQCombination $current_dq */
        $current_dq = $em->getRepository(DQCombination::class)->find($dq_id)[0];
        $status = $current_dq->getEvaluated();
        $current_dq->setEvaluated($status + 1);  // Save in database that this Search was already assessed
        $current_dq->setPostponed(0);  // Make sure that the postponed column is up to date

        try {
            $em->persist($current_dq);
            $em->flush();
        } catch (Exception $e) {
            throw new \Exception("Error while inserting into database");
        }
    }


    /**
     * Create data array with needed information for rendering.
     *
     * @param DQCombination $dq
     * @param $document_data
     * @return array
     */
    private function createArrayForRendering($dq, $document_data) {

        $render_array = array(
            'currently_active_category' => $this->getActiveStudyCategory(),
            'dq_id' => $dq->getId(),
            'q' => $dq->getQuery()->getQuery(),
            'q_id' => $dq->getQuery()->getQuery_Id(),
            'description' => $dq->getQuery()->getDescription(),
            'query_style' => $this->getQueryStyle(),
            'query_heading' => $this->getQueryHeadingName(),
            'topic_heading' => $this->getTopicHeadingName(),
            'doc_id' => $document_data['doc_id'],
            'doc_group' => $document_data['doc_group'],
            'document_heading' => $this->getDocumentHeadingName(),
            'rating_heading' => $this->getRatingHeading(),
            'skipping_allowed' => $this->getSkippingAllowed() ? true : false,
            'skipping_options' => $this->getSkippingOptions(),
            'skipping_options_reject' => Constants::SKIP_REJECT_OPTION,
            'skipping_options_postpone' => Constants::SKIP_POSTPONE_OPTION,
            'skipping_options_both' => Constants::SKIP_BOTH_OPTION,
            'skipping_comment' => $this->getSkippingComment() ? true : false,
            'user_groups' => $this->getUser()->getGroups(),
        );

        // add fields to array depending on selected presentation mode
        if ($this->getAssessmentPresentationMode() == Constants::IFRAME_MODE) {
            $render_array = array_merge($render_array, array(
                    'frame' => $document_data['frame'])
            );
        } else if ($this->getAssessmentPresentationMode() == Constants::DOC_INFO_MODE) {
            $render_array = array_merge($render_array, array(
                    'presentation_fields' => $document_data['presentation_fields'])
            );
        } else if ($this->getAssessmentPresentationMode() == Constants::IMAGE_MODE) {
            $render_array = array_merge($render_array, array(
                    'image' => $document_data['img'])
            );
        } else {
            throw new InvalidParameterException('Invalid presentation mode parameter!');
        }

        return $render_array;
    }

}