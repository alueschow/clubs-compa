<?php

namespace App\Controller\AssessmentModule\Metrics;

use App\Constants;
use App\Entity\AssessmentModule\Metrics\Metric;
use App\Entity\AssessmentModule\Metrics\MetricConfiguration;
use App\Form\AssessmentModule\Metrics\MetricConfigurationType;
use App\Form\AssessmentModule\Metrics\MetricType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MetricsController extends BaseMetricsController
{
    /**
     * @Route("/AssessmentModule/setMetric", name="setMetric")
     *
     * @param Request $request
     * @return Response
     */
    public function setMetricAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$this->getUseMetricsStatus())
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));

        // handle different types of actions for single metrics
        $this->handleMetricGETParams();

        /* Get metric table */
        $repository = $this->getDoctrine()->getRepository(Metric::class);
        $results = $repository->findBy([], ['name' => 'ASC']);

        /* 1) build the form */
        $metric = new Metric();
        $form = $this->createForm(MetricType::class, $metric)
            ->add('submit', SubmitType::class, array('label' => 'Submit metric', 'attr' => ['class' => 'btn btn-secondary']));

        /* 2) handle the submit (will only happen on POST) */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /* 3) make sure no inconsistencies are put into the database */
            if (!$metric->getForCompleteList() && ($metric->getK() <= 0 || $metric->getK() == null)) {
                return $this->redirectToRoute('setMetric', array('metric_invalid' => true));
            }
            if ($metric->getName() === Constants::R_PRECISION) $metric->setForCompleteList(true);
            if ($metric->getForCompleteList()) $metric->setK(-1);  // just a placeholder value because no K is needed in this case
            $metric->setActive(true);

            /* 4) save the Metric */
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($metric);
                $entityManager->flush();
            } catch (\Exception $e) {
                return $this->redirectToRoute('setMetric', array('metric_exists' => true));
            }

            return $this->redirectToRoute('setMetric', array('metric_added' => true));
        }

        return $this->render(
            'assessment_module/metrics/metric_config.html.twig',
            array(
                'form' => $form->createView(), 'results' => $results, 'invalid_value' => Constants::INVALID_VALUE,
                'metric_invalid' => isset($_GET['metric_invalid']) ? true : false,
                'metric_added' => isset($_GET['metric_added']) ? true : false,
                'metric_deleted' => isset($_GET['metric_deleted']) ? true : false,
                'metric_exists' => isset($_GET['metric_exists']) ? true : false,
                'metric_activated' => isset($_GET['metric_activated']) and $_GET['metric_activated'] ? true : false,
                'metric_deactivated' => isset($_GET['metric_deactivated']) and $_GET['metric_deactivated'] ? true : false
            )
        );
    }


    /**
     * @Route("/AssessmentModule/setMetricConfiguration", name="setMetricConfiguration")
     *
     * @param Request $request
     * @return Response
     */
    public function setMetricConfigurationAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$this->getUseMetricsStatus())
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));

        // handle different types of actions
        if (!isset($_GET['updated'])) {
            $update = $this->handleMetricConfigurationGETParams();
            if ($update) return $this->redirectToRoute('setMetricConfiguration', array('updated' => true));
        }

        /* Default configuration is stored in the database under ID == 1
        User configuration is stored under ID == 2 */
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(MetricConfiguration::class);
        $configuration = $repository->find(2);
        /** @var Form $form */
        $form = $this->createForm(MetricConfigurationType::class, $configuration);
        $form->add('new_default', SubmitType::class, array('label' => 'Make this the new default configuration', 'attr' => ['class' => 'btn btn-secondary']))
            ->add('submit', SubmitType::class, array('label' => 'Submit changes', 'attr' => ['class' => 'btn btn-secondary']));

        $form->handleRequest($request);

        /* Set configuration */
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $configuration = new MetricConfiguration();
            $form->get('new_default')->isClicked() ? $configuration->setId(1) : $configuration->setId(2);
            $configuration->setLimited($data->getLimited());
            $configuration->setMaxLength($data->getMaxLength());
            $configuration->setRoundPrecision($data->getRoundPrecision());
            $em->merge($configuration);
            $em->flush();

            return $this->redirectToRoute('setMetricConfiguration', array('update' => true));
        }

        /* Render template */
        return $this->render('assessment_module/metrics/metricconfig_config.html.twig',
            array(
                'form' => $form->createView(),
                'limited_default' => $this->getMetricLimited(true),
                'max_length_default' => $this->getMetricMaxLength(true),
                'round_precision_default' => $this->getMetricRoundPrecision(true),
                'updated' => (isset($_GET['updated']) ? true : false)
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
     * Handle different types of actions for single metrics.
     */
    private function handleMetricGETParams() {
        if (isset($_GET['delete'])) {
            $this->deleteMetric($_GET['delete']);
            return $this->redirectToRoute('setMetric', array('metric_deleted' => true));
        }
        if (isset($_GET['change_status'])) {
            $status = $this->changeMetricStatus($_GET['change_status']);
            return $status
                ? $this->redirectToRoute('setMetric', array('metric_activated' => true))
                : $this->redirectToRoute('setMetric', array('metric_deactivated' => true));
        }
        return $this->redirectToRoute('setMetric');
    }


    /**
     * Handle different types of actions for metrics configuration.
     */
    private function handleMetricConfigurationGETParams() {
        if (isset($_GET['reset'])) {
            $this->resetMetricConfiguration();
            return true;
        }
        return false;
    }


    /**
     * Delete metric with the given ID.
     *
     * @param $id
     */
    private function deleteMetric($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Metric::class);
        $metric = $repository->findBy(['id' => $id])[0];
        $em->remove($metric);
        $em->flush();
    }


    /**
     * (De-)Activate metric with the given ID.
     *
     * @param $id
     * @return boolean representing the new state of the metric
     */
    public function changeMetricStatus($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Metric::class);
        $metric = $repository->findBy(['id' => $id])[0];
        $metric->getActive() ? $metric->setActive(false) : $metric->setActive(true);
        $em->persist($metric);
        $em->flush();

        return $metric->getActive();
    }


    /**
     * Reset metric configuration to default.
     * The default configuration values are stored in the database table under id == 1.
     * User configuration is stored under id == 2.
     */
    public function resetMetricConfiguration()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(MetricConfiguration::class);
        $backup = $repository->find(1);

        $configuration = new MetricConfiguration();
        $configuration->setId(2);
        $configuration->setLimited($backup->getLimited());
        $configuration->setMaxLength($backup->getMaxLength());
        $configuration->setRoundPrecision($backup->getRoundPrecision());
        $em->merge($configuration);
        $em->flush();
    }
}
