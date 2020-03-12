<?php

namespace App\Controller;

use App\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     *
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function adminAction(AuthenticationUtils $authUtils)
    {
        /* redirect the user to the configuration page if he is already logged in */
        if (true === $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if ($this->getUser()->getRoles()[0] === Constants::ROLE_ASSESSMENT) {
                // Redirect user with ROLE_ASSESSMENT to the appropriate page
                return $this->redirectToRoute('assessment');
            } else if ($this->getUser()->getRoles()[0] == Constants::ROLE_ADMIN) {
                // Redirect admin to admin backend
                return $this->redirectToRoute('documentation');
            } else if ($this->getUser()->getRoles()[0] == Constants::ROLE_COMPARISON) {
                // Redirect user with ROLE_COMPARISON to the comparison page
                return $this->redirectToRoute('comparison_standalone');
            } else {
                throw $this->createAccessDeniedException();
            }
        }

        $error = $authUtils->getLastAuthenticationError();  // get the login error if there is one
        $lastUsername = $authUtils->getLastUsername();  // last username entered by the user

        return $this->render('login.html.twig',
            array('last_username' => $lastUsername, 'error' => $error)
        );
    }

}