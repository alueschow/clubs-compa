<?php

namespace App\Controller\ComparisonModule\StandaloneModule\Configuration;

use App\Constants;
use App\Controller\ComparisonModule\StandaloneModule\BaseStandaloneController;
use App\Entity\ComparisonModule\Configuration\StandaloneConfiguration;
use App\Form\ComparisonModule\StandaloneConfigurationType;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class StandaloneConfigurationController extends BaseStandaloneController
{
    /**
     * @Route("/ComparisonModule/setGeneralConfiguration/standalone", name="setGeneralComparisonStandaloneConfiguration")
     *
     * @param $request Request
     * @return Response
     */
    public function setGeneralComparisonStandaloneConfigurationAction(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // configuration is stored in the database under ID == 1

        if ($this->getActiveStudyCategory() != Constants::COMPARISON_CATEGORY_STANDALONE)
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));
        $repository = $this->getDoctrine()->getRepository(StandaloneConfiguration::class);
        $configuration = $repository->find(1);
        $form = $this->createForm(StandaloneConfigurationType::class, $configuration);

        $form->handleRequest($request);

        // set configuration
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data = $this->handleFormData( $form, $data);
            $this->saveConfiguration($data);
            return $this->redirectToRoute('setGeneralComparisonStandaloneConfiguration', array('update' => true));
        }

        // render template
        $render_array = array(
            'form' => $form->createView(),
            'update' => (isset($_GET['update']) ? true : false),
        );

        return $this->render('comparison_module/configuration/standalone_config.html.twig',$render_array);

    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Handle form input: set default values etc.
     *
     * @param $form
     * @param $data
     * @return object $data
     */
    private function handleFormData($form, $data) {
        /** @var Form $form
         * @var Object $data */

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

        // set dependent fields to null values if checkboxes not checked
        if (!$form->getData()->getGroupBy()) {
            $data->setGroupByCategory(Constants::GROUP_BY_DOCUMENT);
            $data->setRandomization(true);
            $data->setDocumentOrder(Constants::DOCUMENT_ORDER_LEFT);
        }
        if ($form->getData()->getRandomization()) {
            $data->setDocumentOrder(Constants::DOCUMENT_ORDER_LEFT);
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

        $configuration = new StandaloneConfiguration();
        $configuration->setId(1);
        $configuration->setPresentationMode($data->getPresentationMode());
        $configuration->setURL($data->getURL());
        $configuration->setPresentationFields($data->getPresentationFields());
        $configuration->setPresentationFieldName1($data->getPresentationFieldName1());
        $configuration->setPresentationFieldName2($data->getPresentationFieldName2());
        $configuration->setPresentationFieldName3($data->getPresentationFieldName3());
        $configuration->setPresentationFieldName4($data->getPresentationFieldName4());
        $configuration->setEvalButtonLeft($data->getEvalButtonLeft());
        $configuration->setEvalButtonRight($data->getEvalButtonRight());
        $configuration->setAllowTie($data->getAllowTie());
        $configuration->setMiddleButton($data->getMiddleButton());
        $configuration->setGroupBy($data->getGroupBy());
        $configuration->setGroupByCategory($data->getGroupByCategory());
        $configuration->setRandomization($data->getRandomization());
        $configuration->setDocumentOrder($data->getDocumentOrder());
        $em->merge($configuration);
        $em->flush();
    }

}