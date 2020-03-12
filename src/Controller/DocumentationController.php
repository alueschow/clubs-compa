<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DocumentationController extends AbstractController
{
    /**
     * @Route("/Documentation/{part}", name="documentation")
     *
     * @param $part
     * @return Response
     */
    public function documentationAction($part=null) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($part == 'getting-started') {
            return $this->render('documentation/getting-started.html.twig');
        } else if ($part == 'database-structure') {
            return $this->render('documentation/database-structure.html.twig');
        } else if ($part == 'importing-data') {
            return $this->render('documentation/importing-data.html.twig');
        } else if ($part == 'looking-at-results') {
            return $this->render('documentation/looking-at-results.html.twig');
        } else if ($part == 'metrics') {
            return $this->render('documentation/metrics.html.twig');
        } else if ($part == 'developer-information') {
            return $this->render('documentation/developer-information.html.twig');
        }

        return $this->render('documentation/documentation.html.twig');
    }

}