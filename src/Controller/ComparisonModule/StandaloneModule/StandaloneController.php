<?php

namespace App\Controller\ComparisonModule\StandaloneModule;

use App\Constants;
use App\Entity\ComparisonModule\Document;
use App\Entity\ComparisonModule\Comparison;
use App\Repository\ComparisonModule\ComparisonRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class StandaloneController extends BaseStandaloneController
{
    /**
     * @Route("/comparison/standalone", name="comparison_standalone")
     *
     * @param Request $request
     * @return Response
     */
    public function comparisonStandaloneAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_COMPARISON');

        if ($this->getActiveStudyCategory() !== Constants::COMPARISON_CATEGORY_STANDALONE)
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));
        if ($this->getActiveMode() !== Constants::COMPARISON_PRODUCTION_MODE && $this->getUser()->getRoles()[0] !== Constants::ROLE_ADMIN)
            return $this->render('messages.html.twig', array('message' => 'no_access'));

        $documents = $this->loadComparisonDocuments();
        $two_documents = $documents['two_documents'];

        if (is_null($documents)) {
            return $this->render('messages.html.twig',
                array('message' => 'success', 'module' => Constants::COMPARISON_MODULE)
            );
        }

        // identify tester with logged in user name
        $tester = $this->getUser()->getUsername();

        // create form
        $form = $this->createRatingForm($two_documents);
        $form->handleRequest($request);

        // save data after rating is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $preferred_document = $_POST['selection'];
            $other_document = $_POST['other_document'];
            $this->getAllowTie()
                ? $tie = $_POST['tie']
                : $tie = null;
            $this->savePagePreference($preferred_document, $tester, $other_document, $tie);

            // load next combination
            return $this->redirectToRoute('comparison_standalone');

        }

        // render template
        return $this->render('comparison_module/comparison.html.twig',
            array(
                'form' => $form->createView(),
                'presentation_mode' => $this->getPresentationMode(),
                'doc_id_left' => $two_documents['left_id'],
                'doc_id_right' => $two_documents['right_id'],
                'left_side' => $two_documents['left_side'],
                'right_side' => $two_documents['right_side'],
                'allow_tie' => $this->getAllowTie(),
            ));
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Load documents from the database to use them in the standalone side-by-side comparison.
     *
     * @return array|null|RedirectResponse|Response
     */
    private function loadComparisonDocuments()
    {
        // get all documents
        $all_documents = $this->getDoctrine()->getRepository(Document::class)->findAll();

        // get remaining combinations that were not yet evaluated by this user
        $remaining_combinations = $this->generateCombinations($all_documents);

        // sort combinations
        if ($this->getGroupBy()) {
            $remaining_combinations = $this->sortCombinations($remaining_combinations);
        }

        if (!empty($remaining_combinations)) {
            // choose two websites to compare
            $two_documents = $this->chooseTwoDocumentsForComparison($remaining_combinations);
            return array('two_documents' => $two_documents);
        } else {
            return null;
        }
    }


    /**
     * Generate combinations for a user that were not evaluated yet.
     *
     * @param $all_documents
     * @return array
     */
    private function generateCombinations($all_documents) {
        // get all previous evaluations from the user
        $combinations = $this->getCombinationsForUser($this->getUser()->getUsername());

        // create an array that contains all document combinations that were already evaluated
        $already_tested_combinations = array();
        for ($i = 0; $i < count($combinations); $i++) {
            $already_tested_combinations[] = array($combinations[$i]['preferred_document'], $combinations[$i]['other_document']);
        }

        // calculate possible combinations for all documents and generate array with remaining documents
        $all_combinations = $this->generateAllCombinations($all_documents);
        $remaining_combinations = $this->generateRemainingCombinations($all_combinations, $already_tested_combinations);

        shuffle($remaining_combinations);

        return $remaining_combinations;
    }


    /**
     * Get all Documents that were already part of a side-by-side comparison of the given user from the Database.
     *
     * @param String $username
     * @return array
     */
    private function getCombinationsForUser($username)
    {
        $repo = $this->getDoctrine()->getRepository(Comparison::class);
        return $repo->findCombinationsForUser($username);
    }


    /**
     * Sort remaining combinations depending on configuration settings.
     *
     * @param $remaining_combinations
     * @return array
     */
    private function sortCombinations($remaining_combinations)
    {
        if ($this->getGroupByCategory() == Constants::GROUP_BY_DOCUMENT) {
            /**
             * @var int $key
             * @var array $row
             */
            // sort the two documents by Document ID
            foreach ($remaining_combinations as $key => $row) {
                if (strcmp($row[0]->getDoc_Id(), $row[1]->getDoc_Id()) > 0) {
                    $tmp = $row[1];
                    $remaining_combinations[$key][1] = $row[0];
                    $remaining_combinations[$key][0] = $tmp;
                }
            }
            // afterwards, sort array by value of first document
            // (which now is always the one earlier in the alphabet)
            usort($remaining_combinations, function ($combination_1, $combination_2) {
                /** @var Document $doc_1
                 * @var Document $doc_2 */
                $doc_1 = $combination_1[0];
                $doc_2 = $combination_2[0];
                return strcmp($doc_1->getDoc_Id(), $doc_2->getDoc_Id());
            });
        } elseif ($this->getGroupByCategory() == Constants::GROUP_BY_DOC_GROUP) {
            /**
             * @var int $key
             * @var array $row
             */
            foreach ($remaining_combinations as $key => $row) {
                if (strcmp($row[0]->getDoc_Group(), $row[1]->getDoc_Group()) > 0) {
                    $tmp = $row[1];
                    $remaining_combinations[$key][1] = $row[0];
                    $remaining_combinations[$key][0] = $tmp;
                }
            }
            // afterwards, sort array by value of first document
            // (which now is always the one earlier in the alphabet)
            usort($remaining_combinations, function ($combination_1, $combination_2) {
                /** @var Document $doc_1
                 * @var Document $doc_2 */
                $doc_1 = $combination_1[0];
                $doc_2 = $combination_2[0];
                return strcmp($doc_1->getDoc_Group(), $doc_2->getDoc_Group());
            });
        }

        return $remaining_combinations;
    }


    /**
     * Create an array with all possible combinations of two documents.
     *
     * @param $all_documents
     * @return array
     */
    private function generateAllCombinations($all_documents)
    {
        $all_combinations = array();
        // iterate over each possible combination of two documents
        for ($i = 0; $i < count($all_documents); $i++) {
            for ($ii = 0; $ii < count($all_documents); $ii++) {
                if ($i != $ii) {  // if both documents are not equal
                    $first_document = $all_documents[$i];
                    $second_document = $all_documents[$ii];

                    /** @var Document $first_document
                     * @var Document $second_document */
                    $first_document_name = $first_document->getDoc_Id();
                    $second_document_name = $second_document->getDoc_Id();

                    // check if this combination is already in array
                    $is_in_array = false;
                    foreach ($all_combinations as $arr) {
                        if (in_array($first_document_name, $arr) and in_array($second_document_name, $arr)) {
                            $is_in_array = true;
                        }
                    }
                    // add to array if combination not already in array
                    if (!$is_in_array) {
                        $all_combinations[] = array($first_document, $second_document);
                    }
                }
                // continue with next combination
            }
        }
        return $all_combinations;
    }


    /**
     * Generate all combinations that were not already evaluated.
     *
     * @param $all_combinations
     * @param $already_tested_combinations
     * @return array
     */
    private function generateRemainingCombinations($all_combinations, $already_tested_combinations)
    {
        $remaining_combinations = array();
        // iterate over each possible combination
        foreach ($all_combinations as $combination_all) {
            $first_document = $combination_all[0];
            $second_document = $combination_all[1];

            /** @var Document $first_document
             * @var Document $second_document */
            $first_document_name = $first_document->getDoc_Id();
            $second_document_name = $second_document->getDoc_Id();

            $already_tested = false;
            // check for each already tested combination if it is equal to the current combination
            if (!empty($already_tested_combinations)) {
                foreach ($already_tested_combinations as $combination_tested) {
                    if (in_array($first_document_name, $combination_tested) and in_array($second_document_name, $combination_tested)) {
                        $already_tested = true;
                    }
                }
            }
            if (!$already_tested) {
                $remaining_combinations[] = array($first_document, $second_document);
            }
        }
        return $remaining_combinations;
    }


    /**
     * Choose two documents (randomly), place one on the left side and the other on the right side.
     *
     * @param $remaining_combinations
     * @return array
     */
    private function chooseTwoDocumentsForComparison($remaining_combinations)
    {
        // set data for left and right side; randomize if desired
        $two_documents = array($remaining_combinations[0][0], $remaining_combinations[0][1]);
        $first_document = null;
        $second_document = null;
        if ($this->getRandomization()) {
            shuffle($two_documents);
            $first_document = $two_documents[0];
            $second_document = $two_documents[1];
        } else if (!$this->getRandomization()) {
            if ($this->getDocumentOrder() == Constants::DOCUMENT_ORDER_RIGHT) {
                $first_document = $two_documents[1];
                $second_document = $two_documents[0];
            } else {
                $first_document = $two_documents[0];
                $second_document = $two_documents[1];
            }
        }

        if ($this->getPresentationMode() == Constants::DOC_INFO_MODE) {
            $left_id = $first_document->getDoc_Id();
            $right_id = $second_document->getDoc_Id();
            $left_side = $this->fillPresentationFields($first_document, $second_document)['left_side'];
            $right_side = $this->fillPresentationFields($first_document, $second_document)['right_side'];
        } else {
            $left_side = $this->getComparisonURL() . $first_document->getDoc_Id();
            $left_id = $first_document->getDoc_Id();
            $right_side = $this->getComparisonURL() . $second_document->getDoc_Id();
            $right_id = $second_document->getDoc_Id();
        }

        return array('left_side' => $left_side, 'left_id' => $left_id,
            'right_side' => $right_side, 'right_id' => $right_id);
    }


    /**
     * Helper function for setting the content of the desired number of document fields.
     *
     * @param $first_document
     * @param $second_document
     * @return array
     */
    private function fillPresentationFields($first_document, $second_document)
    {
        $left_side = $right_side = array();

        /** @var Document $first_document
         * @var Document $second_document */
        if ($this->getPresentationFields() >= 1) {
            if (!is_null($this->getPresentationFieldName1())) {
                $left_side[$this->getPresentationFieldName1()] = html_entity_decode($first_document->getField_1());
                $right_side[$this->getPresentationFieldName1()] = html_entity_decode($second_document->getField_1());
            } else {
                $left_side[1] = html_entity_decode($first_document->getField_1());
                $right_side[1] = html_entity_decode($second_document->getField_1());
            }
        }
        if ($this->getPresentationFields() >= 2) {
            if (!is_null($this->getPresentationFieldName2())) {
                $left_side[$this->getPresentationFieldName2()] = html_entity_decode($first_document->getField_2());
                $right_side[$this->getPresentationFieldName2()] = html_entity_decode($second_document->getField_2());
            } else {
                $left_side[2] = html_entity_decode($first_document->getField_2());
                $right_side[2] = html_entity_decode($second_document->getField_2());
            }
        }
        if ($this->getPresentationFields() >= 3) {
            if (!is_null($this->getPresentationFieldName3())) {
                $left_side[$this->getPresentationFieldName3()] = html_entity_decode($first_document->getField_3());
                $right_side[$this->getPresentationFieldName3()] = html_entity_decode($second_document->getField_3());
            } else {
                $left_side[3] = html_entity_decode($first_document->getField_3());
                $right_side[3] = html_entity_decode($second_document->getField_3());
            }
        }
        if ($this->getPresentationFields() >= 4) {
            if (!is_null($this->getPresentationFieldName4())) {
                $left_side[$this->getPresentationFieldName4()] = html_entity_decode($first_document->getField_4());
                $right_side[$this->getPresentationFieldName4()] = html_entity_decode($second_document->getField_4());
            } else {
                $left_side[4] = html_entity_decode($first_document->getField_4());
                $right_side[4] = html_entity_decode($second_document->getField_4());
            }
        }

        return array('left_side' => $left_side, 'right_side' => $right_side);
    }


    /**
     * Save an evaluation in the database.
     *
     * @param $preferred
     * @param $tester
     * @param $other
     * @param bool $tie
     */
    public function savePagePreference($preferred, $tester, $other, $tie=false)
    {
        $em = $this->getDoctrine()->getManager();

        // insert new evaluation into Comparison database
        $evaluation = new Comparison();
        if ($tie) {
            $evaluation->setPreferred_Document("none");
            $evaluation->setOther_Document($preferred . "///" . $other);
        } else {
            $evaluation->setPreferred_Document($preferred);
            $evaluation->setOther_Document($other);
        }
        $evaluation->setTester($tester);
        $em->persist($evaluation);
        $em->flush();

        // update document database by incrementing 'shown' column
        $preferred = $em->getRepository(Document::class)
            ->findOneBy(array('doc_id' => $preferred));
        $other = $em->getRepository(Document::class)
            ->findOneBy(array('doc_id' => $other));
        $preferred->setShown($preferred->getShown() + 1);
        $other->setShown($other->getShown() + 1);
        $em->persist($preferred);
        $em->persist($other);
        $em->flush();
    }


    /**
     * Create the form for the user's rating of the Documents.
     *
     * @param $two_documents
     * @return FormInterface
     */
    private function createRatingForm($two_documents)
    {
        return $this->createFormBuilder()
            ->add('left_side', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-dark',
                    'onclick' => 'return buttonClick(this, "form_right_side");',
                    'value' => $two_documents['left_id']),
                'label' => $this->getEvalButtonLeft()))
            ->add('middle_button', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-secondary',
                    'onclick' => 'return tieButtonClick(this, "form_left_side", "form_right_side");',
                    'value' => 'tie'),
                'label' => $this->getMiddleButton()))
            ->add('right_side', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-dark',
                    'onclick' => 'return buttonClick(this, "form_left_side");',
                    'value' => $two_documents['right_id']),
                'label' => $this->getEvalButtonRight()))
            ->getForm();
    }

}
