<?php

namespace App\Controller\ComparisonModule\LiveModule\Configuration;

use App\Constants;
use App\Controller\ComparisonModule\LiveModule\BaseLiveController;
use App\Entity\ComparisonModule\Configuration\LiveConfiguration;
use App\Form\ComparisonModule\LiveConfigurationType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class LiveConfigurationController extends BaseLiveController
{
    /**
     * @Route("/ComparisonModule/setGeneralConfiguration/live", name="setGeneralComparisonLiveConfiguration")
     *
     * @param $request Request
     * @return Response
     */
    public function setGeneralComparisonLiveConfigurationAction(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // configuration is stored in the database under ID == 1

        if ($this->getActiveStudyCategory() != Constants::COMPARISON_CATEGORY_LIVE)
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));
        $repository = $this->getDoctrine()->getRepository(LiveConfiguration::class);
        $configuration = $repository->find(1);
        $form = $this->createForm(LiveConfigurationType::class, $configuration);


        $form->handleRequest($request);

        // set configuration
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->saveConfiguration($data);
            return $this->redirectToRoute('setGeneralComparisonLiveConfiguration', array('update' => true));
        }

        // render template
        $render_array = array(
            'form' => $form->createView(),
            'update' => (isset($_GET['update']) ? true : false),
        );
        return $this->render('comparison_module/configuration/live_config.html.twig',$render_array);

    }



    /**
     * ***********************************
     * ************ PRIVATE **************
     * *********** FUNCTIONS *************
     * ***********************************
     */

    /**
     * Save configuration in the database.
     *
     * @param $data Object
     */
    private function saveConfiguration($data)
    {
        $em = $this->getDoctrine()->getManager();

        $configuration = new LiveConfiguration();
        $configuration->setId(1);
        $configuration->setUseBaseWebsite($data->getUseBaseWebsite());
        $configuration->setDocumentOrder($data->getDocumentOrder());
        $configuration->setEvalButtonLeft($data->getEvalButtonLeft());
        $configuration->setEvalButtonRight($data->getEvalButtonRight());
        $configuration->setAllowTie($data->getAllowTie());
        $configuration->setMiddleButton($data->getMiddleButton());
        $configuration->setRandomization($data->getRandomization());
        $configuration->setRandomizationParticipation($data->getRandomizationParticipation());
        $configuration->setCookieName($data->getCookieName());
        $configuration->setCookieExpires($data->getCookieExpires());
        $configuration->setParticipationsPerTimeSpan($data->getParticipationsPerTimeSpan());
        $configuration->setTimeSpan($data->getTimeSpan());
        $em->merge($configuration);
        $em->flush();
    }

}