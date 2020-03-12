<?php

namespace App\Controller\ComparisonModule\LiveModule;

use App\Constants;
use App\Entity\ComparisonModule\Configuration\Website;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;


class FakeSearchController extends BaseLiveController
{
//    TODO
    /**
     * @Route("/fakesearch", name="fakesearch")
     * @return Response
     */
    public function fakeSearchAction()
    {
        if ($this->getActiveStudyCategory() == Constants::COMPARISON_CATEGORY_LIVE)
            return $this->render('comparison_module/fakesearch.html.twig');
        return $this->render('messages.html.twig', array('message' => 'module_not_loaded'));

    }


    /**
     * @Route("/checkUserParameters/{id}", name="checkUserParametersForwarding", options = {"expose": true})
     *
     * @param $id
     * @return Response
     */
    public function checkUserParametersForwarding($id)
    {
        $q = $_GET['query'];

        $tmp = "";
        /* If id == 1, redirect to the comparison page; else don't show comparison. */
        if ($id == 1) {
            $tmp = $this->redirectToRoute('comparison_live', array('query' => $q));
        } else if ($id == 2) {
            $base = $this->getDoctrine()->getRepository(Website::class)->findOneBy(["is_base_website", true]);
            $tmp =  $this->redirect($base->getWebsiteURL() . $q);
        }
        return $tmp;
    }


    /**
     * @Route("/checkUserParameters", name="checkUserParameters")
     *
     * @param Request $request
     * @return Response
     */
    public function checkUserParametersAction(Request $request)
    {
        $q = $_GET['query'];
        $base = $this->getDoctrine()->getRepository(Website::class)->findOneBy(["is_base_website" => true]);

        /* Get Cookie information */
        $cookies = $request->cookies->all();
        $cookie = json_decode($cookies[strval($this->getCookieName())], true);

        /* if last usage of the app is more than the specified time ago, set participation counter back to 0 */
        if($this->lastAppUsage($cookie) > $this->getTimeSpan() * 60) {
            $cookie['times'] = 0;
        }

        /* If maximum number of participations is not reached yet,
        decide (randomly) whether to show the page comparison to the user or not */
        $nr_of_participations = $cookie['times'];
        if ($nr_of_participations < $this->getParticipationsPerTimeSpan()) {
            if (!$this->getRandomizationParticipation()) {
                $random = rand(1,2);
            } else {
                $random = 1;
            }
            if($random == 1) {
                $cookie['times'] = $nr_of_participations + 1;
                // TODO [ONGOING]: Parameters 'part', 'times', 'maxpart' and 'time_span' only for development!
                // TODO [BUG]: checking for browser settings not working in production environment due to disabled fos-js-routing bundle
                // $response = $this->render('check-user-parameters.html.twig', array('q' => $q, 'r' => $r, 'part' => $cookie['participation'], 'times' => $cookie['times'], 'maxpart' => $this->participationsPerTimeSpan(), 'time_span' => $this->timeSpan()));
                $response = $this->redirectToRoute('checkUserParametersForwarding', array('id' => 1, 'query' => $q));
            } else {
                $response = $this->redirect($base->getWebsiteURL() . $q);
            }

            /* update Cookie */
            $cookie['lastusage'] = date('d/m/Y h:i:s a', time());  // set last usage to current time
            $data = json_encode($cookie);
            $cookie_final = new Cookie($this->getCookieName(), $data, time() + ($this->getCookieExpireTime() * 86400), '/');
            $response->headers->setCookie($cookie_final);

            return $response;
        } else {  // if maximum is reached, redirect to original page
            return $this->redirect($base->getWebsiteURL() . $q);
        }

    }



    /**
     * ***********************************
     * *********** PRIVATE ***************
     * ********** FUNCTIONS **************
     * ***********************************
     */

    /**
     * Calculates the time delta between the last usage of the app by the user and now.
     *
     * @param $cookie
     * @return double
     */
    private function lastAppUsage($cookie) {
        $lu = strtotime($cookie['lastusage']);
        $now = strtotime(date('d/m/Y h:i:s a', time()));
        return round(abs($lu - $now) / 60, 2);
    }

}
