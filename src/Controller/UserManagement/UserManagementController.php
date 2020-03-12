<?php

namespace App\Controller\UserManagement;

use App\Constants;
use App\DatabaseUtils;
use App\Entity\User;
use App\Form\UserManagement\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserManagementController extends AbstractController
{
    /**
     * @Route("/UserManagement", name="userManagement")
     * Action for different module pages
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function adminModulesAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // handle GET parameters
        $get_params = $this->handleGETParams();
        if ($get_params['user_deleted']) return $this->redirectToRoute('userManagement');

        // get users from repository
        $results = null;
        $repository = $this->getDoctrine()->getRepository(User::class);
        foreach (Constants::allRoles() as $role) {
            $results[$role] = array();
            $all = $repository->findBy([], array('roles' => 'ASC', 'username' => 'ASC'));
            foreach($all as $a) {
                if (in_array($role, $a->getRoles())) {
                    $results[$role][] = $a;
                }
            }
        }

        // build form for new users and handle submission
        $user = new User();
        $options = array();
        // get document groups
        DatabaseUtils::setEntityManager($this->getDoctrine()->getManager());
        foreach (DatabaseUtils::getDocumentGroups()['document_groups'] as $key => $row) {
            $options['document_groups'][$row] = $row;
        }
        $form = $this->createForm(UserType::class, $user, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            if (empty($user->getGroups())) $user->setGroups('');
            $this->addUser($user);
            return $this->redirectToRoute('userManagement', array('user_added' => $user->getUsername()));
        }

        return $this->render(
            'user_management/user_management.html.twig',
            array(
                'form' => $form->createView(),
                'results' => $results,
                'user_added' => $get_params['user_added']
            )
        );
    }



    /**
     * ***********************************
     * *********** PRIVATE ***************
     * ********** FUNCTIONS **************
     * ***********************************
     */

    /**
     * Handle get parameters.
     *
     * @return array
     */
    private function handleGETParams() {
        $user_added = false;
        $user_deleted = false;

        if (isset($_GET['user_added'])) {
            $user_added = $_GET['user_added'];
        } else if (isset($_GET['delete'])) {
            $this->deleteUser($_GET['delete']);
            $user_deleted = true;
        }

        return array('user_added' => $user_added, 'user_deleted' => $user_deleted);
    }


    /**
     * @param $user
     * @return void
     */
    private function addUser($user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }


    /**
     * @param $name
     * @return void
     */
    private function deleteUser($name)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findBy(['username' => $name])[0];

        $em->remove($user);
        $em->flush();
    }

}