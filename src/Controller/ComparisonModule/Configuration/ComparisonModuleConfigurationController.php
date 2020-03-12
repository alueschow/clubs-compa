<?php

namespace App\Controller\ComparisonModule\Configuration;

use App\Constants;
use App\Controller\ComparisonModule\BaseController;
use App\Entity\ComparisonModule\Configuration\MainConfiguration;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class ComparisonModuleConfigurationController extends BaseController
{

    /**
     * @Route("/ComparisonModule", name="comparisonModule")
     *
     * @return Response
     */
    public function showComparisonModule()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // handle different types of settings that are not part of the form
        $this->handleGETParams();

        return $this->render('comparison_module/comparison_module.html.twig',
            array(
                'application_modes' => Constants::ComparisonApplicationModes(),
                'study_categories' => Constants::ComparisonstudyCategories(),
                'update' => (isset($_GET['update']) ? true : false),
                'setting' => (isset($_GET['setting']) ? $_GET['setting'] : null),
                'change_category' => (isset($_GET['change_category']) ? $_GET['change_category'] : null)
            )
        );
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
        $debug_mode = null;
        if (isset($_GET['setting'])) {
            if ($_GET['setting'] == 'switch_debug') {
                $debug_mode = $this->changeDebugModeStatus();
            }
        } else if (isset($_GET['change_category'])) {
            $this->changeActiveStudyCategory($_GET['change_category']);
        } else if (isset($_GET['change_mode'])) {
            $this->changeMode($_GET['change_mode']);
        }
        return $debug_mode;
    }


    /**
     * Switch debugging mode on or off.
     *
     * @return string
     */
    public function changeDebugModeStatus()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(MainConfiguration::class);
        $current_configuration = $repository->find(1);
        $current_configuration->getDebugModeStatus()
            ? $mode_change = "off"
            : $mode_change = "on";
        $current_configuration->getDebugModeStatus()
            ? $current_configuration->setDebugModeStatus(false)
            : $current_configuration->setDebugModeStatus(true);
        $em->merge($current_configuration);
        $em->flush();

        return $mode_change;
    }


    /**
     * Change the currently active study category according to the passed parameter.
     *
     * @param $category_name
     */
    public function changeActiveStudyCategory($category_name)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(MainConfiguration::class);
        $current_configuration = $repository->find(1);
        $current_configuration->setActiveStudyCategory($category_name);
        $em->merge($current_configuration);
        $em->flush();
    }


    /**
     * Change the currently active rating category according to the passed parameter.
     *
     * @param $mode
     */
    public function changeMode($mode)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(MainConfiguration::class);
        $current_configuration = $repository->find(1);
        $current_configuration->setMode($mode);
        $em->merge($current_configuration);
        $em->flush();
    }

}