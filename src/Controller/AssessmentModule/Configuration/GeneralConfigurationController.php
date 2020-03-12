<?php

namespace App\Controller\AssessmentModule\Configuration;

use App\Constants;
use App\Controller\AssessmentModule\BaseController;
use App\Entity\AssessmentModule\Configuration\GeneralConfiguration;
use App\Form\AssessmentModule\GeneralConfigurationType;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class GeneralConfigurationController extends BaseController
{
    /**
     * Create a form for changing/resetting configuration of the Assessment module and show it to the user.
     *
     * @Route("/AssessmentModule/setGeneralConfiguration", name="setGeneralAssessmentConfiguration")
     *
     * @param $request Request
     * @return Response
     */
    public function setGeneralAssessmentConfigurationAction(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // handle different types of settings that are not part of the form
        // *currently not needed and thus not in use*
//        $this->handleGETParams();

        // configuration is stored under ID == 1 in the database table
        $repository = $this->getDoctrine()->getRepository(GeneralConfiguration::class);
        $configuration = $repository->find(1);

        // create form
        $form = $this->createForm(GeneralConfigurationType::class, $configuration);
        $form->handleRequest($request);

        // set configuration
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data = $this->handleFormData($form, $data);
            $this->saveConfiguration($data);

            return $this->redirectToRoute('setGeneralAssessmentConfiguration', array('update' => true));
        }

        // render template
        return $this->render('assessment_module/configuration/general_config.html.twig',
            array(
                'form' => $form->createView(),
                'update' => (isset($_GET['update']) ? true : false),
            ));

    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Handle different types of settings that are not part of the form.
     */
    private function handleGETParams() {
        // do nothing
    }


    /**
     * Handle form input: set default values etc.
     *
     * @param $form
     * @param $data
     * @return object $data
     */
    private function handleFormData($form, $data) {
        /**
         * @var Form $form
         * @var Object $data
         */
        // prevent URL field vom being empty
        if (strlen($form->getData()->getUrl()) == 0)
            $data->setUrl("");
        // set default values if presentation fields are empty
        if (!$form->getData()->getPresentationFieldName1()) {
            $data->setPresentationFieldName1(null);
        }
        if (!$form->getData()->getPresentationFieldName2()) {
            $data->setPresentationFieldName2(null);
        }
        if (!$form->getData()->getPresentationFieldName3()) {
            $data->setPresentationFieldName3(null);
        }
        if (!$form->getData()->getPresentationFieldName4()) {
            $data->setPresentationFieldName4(null);
        }
        // default query style
        if (!$form->getData()->getQueryStyle()) {
            $data->setQueryStyle('both');
        }
        // set default values if query/topic/document heading empty
        if (strlen($form->getData()->getQueryHeadingName()) == 0) {
            $data->setQueryHeadingName(null);
        }
        if (strlen($form->getData()->getTopicHeadingName()) == 0) {
            $data->setTopicHeadingName(null);
        }
        if (!$form->getData()->getDocumentHeading() or strlen($form->getData()->getDocumentHeadingName()) == 0) {
            $data->setDocumentHeadingName(null);
        }
        // default value if no grouping desired
        if (!$form->getData()->getGroupBy()) {
            $data->setGroupByCategory('query');
        }
        // default values for skipping options
        if (!$form->getData()->getSkippingAllowed()) {
            $data->setSkippingOptions(Constants::SKIP_REJECT_OPTION);
            $data->setSkippingComment(null);
        } else {
            // no comment possible when only postponing allowed
            if ($form->getData()->getSkippingOptions() == Constants::SKIP_POSTPONE_OPTION) {
                $data->setSkippingComment(null);
            }
        }

        return $data;
    }


    /**
     * Save configuration in the database.
     *
     * @param $data Object
     */
    private function saveConfiguration($data)
    {
        $em = $this->getDoctrine()->getManager();
        $configuration = new GeneralConfiguration();
        $configuration->setId(1);

        // copy rating heading from current configuration
        $configuration->setRatingHeading($this->getRatingHeading());

        // take values from passed data
        $configuration->setURL($data->getURL());
        $configuration->setPresentationMode($data->getPresentationMode());
        $configuration->setGroupBy($data->getGroupBy());
        $configuration->setGroupByCategory($data->getGroupByCategory());
        $configuration->setSkippingAllowed($data->getSkippingAllowed());
        $configuration->setSkippingOptions($data->getSkippingOptions());
        $configuration->setSkippingComment($data->getSkippingComment());
        $configuration->setLoadingNewDocument($data->getLoadingNewDocument());
        $configuration->setUserProgressBar($data->getUserProgressBar());
        $configuration->setTotalProgressBar($data->getTotalProgressBar());
        $configuration->setPresentationFields($data->getPresentationFields());
        $configuration->setPresentationFieldName1($data->getPresentationFieldName1());
        $configuration->setPresentationFieldName2($data->getPresentationFieldName2());
        $configuration->setPresentationFieldName3($data->getPresentationFieldName3());
        $configuration->setPresentationFieldName4($data->getPresentationFieldName4());
        $configuration->setQueryStyle($data->getQueryStyle());
        $configuration->setQueryHeadingName($data->getQueryHeadingName());
        $configuration->setTopicHeadingName($data->getTopicHeadingName());
        $configuration->setDocumentHeading($data->getDocumentHeading());
        $configuration->setDocumentHeadingName($data->getDocumentHeadingName());
        $em->merge($configuration);
        $em->flush();
    }

}