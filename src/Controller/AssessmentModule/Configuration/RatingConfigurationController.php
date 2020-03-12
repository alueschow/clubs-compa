<?php

namespace App\Controller\AssessmentModule\Configuration;

use App\Constants;
use App\Controller\AssessmentModule\BaseController;
use App\Entity\AssessmentModule\Configuration\GeneralConfiguration;
use App\Entity\AssessmentModule\Configuration\RatingOption;
use App\Form\AssessmentModule\RatingOptionType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RatingConfigurationController extends BaseController
{
    /**
     * @Route("/AssessmentModule/setRatingOptionConfiguration", name="setRatingOptionConfiguration")
     *
     * @param $request Request
     * @return Response
     */
    public function setRatingOptionConfigurationAction(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // handle different types of actions for single rating options
        $get_params = $this->handleGETParams();

        // create and fill form
        $repository = $this->getDoctrine()->getRepository(RatingOption::class);
        $results = $repository->findBy(array(), ['priority' => 'DESC']);
        $rating_level = new RatingOption();
        $form = $this->createForm(RatingOptionType::class, $rating_level);

        $form->handleRequest($request);

        /* Set configuration */
        if ($form->isSubmitted() && $form->isValid()) {
            // set default values
            if ($this->getActiveStudyCategory() == Constants::CLASSIFICATION_CATEGORY) {
                // option not used in Classification category, thus always set to false
                $rating_level->setUsedInMetrics(false);
            }

            // save the rating option
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($rating_level);
                $entityManager->flush();
            } catch (\Exception $e) {
                return $this->redirectToRoute('setRatingOptionConfiguration', array('rating_option_exists' => true));
            }
            return $this->redirectToRoute('setRatingOptionConfiguration', array('rating_option_added' => true));
        }

        /* Render template */
        return $this->render(
            'assessment_module/configuration/rating_option_config.html.twig',
            array(
                'form' => $form->createView(), 'results' => $results, 'active_category' => $this->getActiveStudyCategory(),
                'rating_heading' => $this->getRatingHeading(),
                'rating_option_added' => isset($_GET['rating_option_added']) ? true : false,
                'rating_option_exists' => isset($_GET['rating_option_exists']) ? true : false,
                'rating_option_deleted' => $get_params['rating_option_deleted'],
                'metrics_activated' => $get_params['metrics_activated'],
                'metrics_deactivated' => $get_params['metrics_deactivated'],
                'heading_changed' => $get_params['heading_changed'],
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
     * Handle different types of actions for single rating options.
     */
    private function handleGETParams() {
        $rating_option_deleted = false;
        $metrics_activated = false;
        $metrics_deactivated = false;
        $heading_changed = false;

        if (isset($_GET['delete'])) {
            $this->deleteRatingLevel($_GET['delete']);
            $rating_option_deleted = true;
        }
        if (isset($_GET['change_metrics_status'])) {
            $status = $this->changeUsedInMetricsStatus($_GET['change_metrics_status']);
            $status
                ? $metrics_activated = true
                : $metrics_deactivated = true;
        }
        if (isset($_GET['change_rating_heading'])) {
            $this->changeRatingHeading($_GET['change_rating_heading']);
            $heading_changed = true;
        }

        return array(
            'rating_option_deleted' => $rating_option_deleted,
            'metrics_activated' => $metrics_activated,
            'metrics_deactivated' => $metrics_deactivated,
            'heading_changed' => $heading_changed
        );
    }


    /**
     * Change the heading of the rating form.
     *
     * @param $heading
     */
    private function changeRatingHeading($heading)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(GeneralConfiguration::class);
        $config = $repository->findBy(['id' => 1])[0];
        $config->setRatingHeading($heading);
        $em->persist($config);
        $em->flush();
    }


    /**
     * Delete rating option with the given ID.
     *
     * @param $id
     */
    private function deleteRatingLevel($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(RatingOption::class);
        $rating_level = $repository->findBy(['id' => $id])[0];
        $em->remove($rating_level);
        $em->flush();
    }


    /**
     * Change the status of the 'used in metrics' option for the rating option with the given ID.
     *
     * @param $id
     * @return boolean representing the new state of the 'used in metrics' option
     */
    private function changeUsedInMetricsStatus($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(RatingOption::class);
        $rating_level = $repository->findBy(['id' => $id])[0];
        $rating_level->getUsedInMetrics()
            ? $rating_level->setUsedInMetrics(false)
            : $rating_level->setUsedInMetrics(true);
        $em->persist($rating_level);
        $em->flush();

        return $rating_level->getUsedInMetrics();
    }

}