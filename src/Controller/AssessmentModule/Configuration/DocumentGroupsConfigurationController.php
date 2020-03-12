<?php

namespace App\Controller\AssessmentModule\Configuration;

use App\Controller\AssessmentModule\BaseController;
use App\DatabaseUtils;
use App\Entity\AssessmentModule\Configuration\DocumentGroup;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class DocumentGroupsConfigurationController extends BaseController
{
    /**
     * @Route("/AssessmentModule/setDocumentGroupConfiguration", name="setDocumentGroupConfiguration")
     *
     * @return Response
     */
    public function setDocumentGroupsConfigurationAction() {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // handle different types of actions for document groups
        $get_params = $this->handleGETParams();

        // create and fill form
        $repository = $this->getDoctrine()->getRepository(DocumentGroup::class);
        $doc_groups = $repository->findBy([], ['name' => 'ASC']);

        DatabaseUtils::setEntityManager($this->getDoctrine()->getManager());
        $user_groups = DatabaseUtils::countUserGroups();

        /* Render template */
        return $this->render(
            'assessment_module/configuration/document_group_config.html.twig',
            array(
                'doc_groups' => $doc_groups,
                'user_groups' => $user_groups,
                'updated' => $get_params['update'],
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
     * Handle GET parameters
     */
    private function handleGETParams() {
        $update = false;

        if (isset($_GET['update'])) {
            $this->updateDocumentGroups();
            $update = true;
        }

        return array('update' => $update);
    }


    /**
     * Update max nr of evaluations for all document groups based on GET form.
     */
    private function updateDocumentGroups()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(DocumentGroup::class);
        $results = $repository->findBy([], ['name' => 'ASC']);

        foreach ($results as $item) {
            $group = $repository->findBy(['id' => $item->getId()])[0];
            $group->setNrOfMaxEvaluations($_GET[$item->getName()]);
            $em->persist($group);
            $em->flush();
        }
    }

}