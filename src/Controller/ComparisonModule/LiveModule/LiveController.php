<?php

namespace App\Controller\ComparisonModule\LiveModule;

use App\Constants;
use App\Entity\ComparisonModule\Configuration\Website;
use App\Entity\ComparisonModule\Comparison;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class LiveController extends BaseLiveController
{
    /**
     * @Route("/comparison/live", name="comparison_live")
     *
     * @param Request $request
     * @return Response
     */
    public function comparisonLiveAction(Request $request)
    {
        if ($this->getActiveStudyCategory() != Constants::COMPARISON_CATEGORY_LIVE)
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));

        $tester = $request->getClientIp();  // Identify tester with IP
        $query = $_GET['query'];
        $two_documents = $this->loadWebsites();

        $form = $this->createRatingForm($two_documents);
        $form->handleRequest($request);

        // save data after rating is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $preferred_document = $_POST['selection'];
            $other_document = $_POST['other_document'];
            $this->getAllowTie()
                ? $tie = $_POST['tie']
                : $tie = null;

            empty($tie)
                ? $this->savePagePreference($query, $preferred_document, $tester, $other_document)
                : $this->savePagePreference($query, $preferred_document, $tester, $other_document, true);

            $preferred_website_url = $this->getWebsiteURL($preferred_document);
            $fullscreen = $preferred_website_url . $query;
            return $this->redirect($fullscreen);  // Show selected page in fullscreen

        }

        // render template
        return $this->render('comparison_module/comparison.html.twig',
            array(
                'form' => $form->createView(),
                'left_side' => $two_documents['left_side'],
                'right_side' => $two_documents['right_side'],
                'presentation_mode' => Constants::COMPARISON_MODULE_LIVE,
                'query' => $query,
                'allow_tie' => $this->getAllowTie()
            ));
    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Load two websites that will be compared in live module.
     */
    private function loadWebsites() {
        // get query and all websites
        $query = $_GET['query'];
        $all_websites = $this->getDoctrine()->getRepository(Website::class)->findAll();

        // get base website if needed
        $this->getUseBaseWebsite()
            ? $base_website_name = $this->getDoctrine()->getRepository(Website::class)->findOneBy(['is_base_website' => true])->getWebsiteName()
            : $base_website_name = null;

        // calculate possible combinations for all websites
        $all_combinations = $this->generateAllCombinations($all_websites, $base_website_name);

        // choose two websites
        $two_documents = $this->chooseTwoDocumentsForComparison( $all_combinations, $query);

        return $two_documents;
    }


    /**
     * Create an array with all possible combinations of two documents.
     *
     * @param $all_documents
     * @param null $base_website_name
     * @return array
     */
    private function generateAllCombinations($all_documents, $base_website_name=null)
    {
        $all_combinations = array();
        // iterate over each possible combination of two documents
        for ($i = 0; $i < count($all_documents); $i++) {
            for ($ii = 0; $ii < count($all_documents); $ii++) {
                if ($i != $ii) {  // if both documents are not equal
                    $first_document = $all_documents[$i];
                    $second_document = $all_documents[$ii];

                    /** @var Website $first_document
                     * @var Website $second_document */
                    $first_document_name = $first_document->getWebsiteName();
                    $second_document_name = $second_document->getWebsiteName();

                    // take only pairs that contain the base_website into account
                    if ($this->getUseBaseWebsite()) {
                        if ($first_document_name != $base_website_name and $second_document_name != $base_website_name) {
                            continue;
                        }
                    }

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
     * Choose two documents (randomly), place one on the left side and the other on the right side.
     *
     * @param $remaining_combinations
     * @param null $query
     * @return array
     */
    private function chooseTwoDocumentsForComparison($remaining_combinations, $query=null)
    {
        shuffle($remaining_combinations);

        // set data for left and right side
        /** @var Website $first_document
         * @var Website $second_document */
        $first_document = $remaining_combinations[0][0];
        $second_document = $remaining_combinations[0][1];

        if ($this->getUseBaseWebsite()) {
            // sort website presentation if no random order desired
            if (!$this->getRandomization()) {
                if ((!$first_document->getIsBaseWebsite() and $this->getDocumentOrder() == Constants::DOCUMENT_ORDER_LEFT) or
                    ($first_document->getIsBaseWebsite() and $this->getDocumentOrder() == Constants::DOCUMENT_ORDER_RIGHT)) {
                    $tmp = $first_document;
                    $first_document = $second_document;
                    $second_document = $tmp;
                }
            }
        }

        $left_side = $first_document->getWebsiteURL() . $query;
        $left_name = $first_document->getWebsiteName();
        $right_side = $second_document->getWebsiteURL() . $query;
        $right_name = $second_document->getWebsiteName();

        return array('left_side' => $left_side, 'left_name' => $left_name,
            'right_side' => $right_side, 'right_name' => $right_name);
    }


    /**
     * Query the database for the URL of a given website name.
     *
     * @param $website
     * @return String
     */
    private function getWebsiteURL($website)
    {
        return $this->getDoctrine()->getRepository(Website::class)
            ->findOneBy(array('website_name' => $website))->getWebsiteURL();
    }


    /**
     * Save an evaluation in the database.
     *
     * @param $query
     * @param $preferred
     * @param $tester
     * @param $other
     * @param bool $tie
     */
    public function savePagePreference($query, $preferred, $tester, $other, $tie=false)
    {
        $em = $this->getDoctrine()->getManager();

        // insert new evaluation into Comparison database
        $evaluation = new Comparison();
        $evaluation->setQuery($query);
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
        $preferred = $em->getRepository(Website::class)
            ->findOneBy(array('website_name' => $preferred));
        $other = $em->getRepository(Website::class)
            ->findOneBy(array('website_name' => $other));
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
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createRatingForm($two_documents)
    {
        return $this->createFormBuilder()
            ->add('left_side', SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-dark',
                    'onclick' => 'return buttonClick(this, "form_right_side");',
                    'value' => $two_documents['left_name']),
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
                    'value' => $two_documents['right_name']),
                'label' => $this->getEvalButtonRight()))
            ->getForm();
    }

}
