<?php

namespace App\Controller\ComparisonModule\LiveModule\Configuration;

use App\Constants;
use App\Controller\ComparisonModule\LiveModule\BaseLiveController;
use App\Entity\ComparisonModule\Configuration\Website;
use App\Form\ComparisonModule\WebsiteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WebsiteConfigurationController extends BaseLiveController
{
    /**
     * @Route("/ComparisonModule/setWebsiteConfiguration", name="setWebsiteConfiguration")
     * @param Request $request
     * @return Response
     */
    public function registerWebsiteAction(Request $request)
    {
        if ($this->getActiveStudyCategory() != Constants::COMPARISON_CATEGORY_LIVE)
            return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));
        $get_params = $this->handleGETParams();

        // get website table
        $repository = $this->getDoctrine()->getRepository(Website::class);
        $results = $repository->findAll();

        // build the form
        $website = new Website();
        $form = $this->createForm(WebsiteType::class, $website);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($website);
            $em->flush();

            return $this->redirectToRoute('setWebsiteConfiguration', array('added' => true));
        }

        return $this->render(
            'comparison_module/configuration/website_config.html.twig',
            array(
                'form' => $form->createView(),
                'results' => $results,
                'use_base_website' => $this->getUseBaseWebsite(),
                'added' => isset($_GET['added']) ? true : null,
                'deleted' => $get_params['deleted'],
                'base_changed' => $get_params['base_changed'] ? true : null,
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
     * Handle get parameters.
     *
     * @return array
     */
    private function handleGETParams() {
        $deleted = false;
        $base_changed = false;

        if (isset($_GET['delete'])) {
            $this->deleteWebsite($_GET['delete']);
            $deleted = true;
        }
        if (isset($_GET['change_base'])) {
            if ($this->getUseBaseWebsite()) {
                $this->changeBaseWebsite($_GET['change_base']);
                $base_changed = true;
            }
        }

        return array('deleted' => $deleted, 'base_changed' => $base_changed);
    }


    /**
     * @param $id
     * @return Response
     */
    private function changeBaseWebsite($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Website::class);
        $websites = $repository->findAll();
        foreach ($websites as $website) {
            if ($website->getId() == $id) {
                $website->setIsBaseWebsite(true);
                $em->merge($website);
                $em->flush();
            } else {
                $website->setIsBaseWebsite(false);
                $em->merge($website);
                $em->flush();
            }
        }

        return $this->redirectToRoute('setWebsiteConfiguration', array('base_changed' => true));

    }


    /**
     * @param $id
     * @return Response
     */
    private function deleteWebsite($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Website::class);
        $website = $repository->findBy(['id' => $id])[0];
        $em->remove($website);
        $em->flush();

        return $this->redirectToRoute('setWebsiteConfiguration', array('deleted' => true));

    }
}
