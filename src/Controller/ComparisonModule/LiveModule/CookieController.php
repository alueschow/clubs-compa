<?php

namespace App\Controller\ComparisonModule\LiveModule;

use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class CookieController extends BaseLiveController
{
//    TODO
    /**
     * @Route("/interception", name="interception")
     *
     * @param Request $request
     * @return Response
     */
    public function interceptionAction(Request $request)
    {
        $response = new Response();

        // get query and cookies from HTTP request
        $cookies = $request->cookies->all();
        $query = $_GET['query'];

        // check if cookie is set and redirect respectively
        if (isset($cookies[strval($this->getCookieName())])) {
            // get Cookie variables
            $cookie = json_decode($cookies[strval($this->getCookieName())], true);
            $participation = $cookie['participation'];

            /* If cookie is set to TRUE, check if user parameters are correct.
            If cookie is set to FALSE, redirect to original page (== no participation). */
            if ($participation == "true") {
                return $this->redirectToRoute('checkUserParameters', array('query' => $query));
            } else if ($participation == "false") {
                return $this->redirect($this->getOriginalPage() . $query);
            }
        } else {  // if Cookie is not set
            /**
             * Create a form with 'Yes' and 'No' option
             * @var Form $form
             */
            $form = $this->createFormBuilder()
                ->add('yes', SubmitType::class, array('attr' => array('class' => 'col-md-3 btn btn-secondary'), 'label' => 'Yes'))
                ->add('no', SubmitType::class, array('attr' => array('class' => 'col-md-3 col-md-offset-1 btn btn-secondary'), 'label' => 'No'))
                ->getForm();
            $form->handleRequest($request);

            /* Handle form submission.
            If user wants to participate, set cookie to TRUE, (randomize) and check if user parameters are correct.
            If user does not want to participate, set cookie to FALSE and redirect to original page. */
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->getClickedButton()->getName() === 'yes') {
                    $cookie = $this->getNewTrueCookie();
                    $response = $this->redirectToRoute('checkUserParameters', array('query' => $query));
                } else {
                    $cookie = $this->getFalseCookie();
                    $response = $this->redirect($this->getOriginalPage() . $query);
                }
                /* Set header's cookie and return Response object */
                $response->headers->setCookie($cookie);
                return $response;
            }
            return $this->render('messages.html.twig', array('message' => 'ask_for_cookie', 'form' => $form->createView()));
        }
        return $response;
    }


    /**
     * @Route("/deleteCookie", name="deleteCookie")
     * Sets cookie to false (==deletion) and redirects to the original page.
     *
     * @return Response
     */
    public function deleteCookieAction()
    {
        $query = $_GET['query'];
        $cookie = $this->getFalseCookie();
        $response = $this->render('messages.html.twig', array('message' => 'cookie_set_to_false', 'original_page' => $this->getOriginalPage() . $query));
        $response->headers->setCookie($cookie);
        return $response;
    }


    /**
     * ***********************************
     * *********** PRIVATE ***************
     * ********** FUNCTIONS **************
     * ***********************************
     */

    /**
     * Creates a new cookie with participation == true.
     *
     * @return Cookie
     */
    private function getNewTrueCookie() {
        return new Cookie($this->getCookieName(),
            json_encode(
                array('participation' => 'true', 'times' => 0, 'lastusage' => date('d/m/Y h:i:s a', time()))
            ),
            time() + ($this->getCookieExpireTime() * 86400),
            '/');
    }


    /**
     * Sets the participation element of a cookie to false.
     *
     * @return Cookie
     */
    private function getFalseCookie() {
        return new Cookie($this->getCookieName(),
            json_encode(
                array('participation' => 'false')),
            time() + ($this->getCookieExpireTime() * 86400),
            '/');
    }

}