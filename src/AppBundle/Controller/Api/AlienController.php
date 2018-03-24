<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use AppBundle\Entity\Alien;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use AppBundle\Form\RegistrationFormType;
use FOS\UserBundle\FOSUserEvents;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Security("is_granted('ROLE_USER')")
 * Class AlienController
 * @package AppBundle\Controller\Api
 */
class AlienController extends BaseController
{


    /**
     * @Route("/api/alien/{username}",name="api_alien_show")
     * @Method("GET")
     */
    public function showAction($username)
    {

        $alien = $this->getDoctrine()
            ->getRepository('AppBundle:Alien')
            ->findOneBy(array('username' => $username));
        if (!$alien)
            throw $this->createNotFoundException('No alien found for username '.$username);

        $response  = $this->createApiResponse($alien);
        return $response;
    }

    /**
     * @Route("/api/aliens",name="api_aliens_collection")
     * @Method("GET")
     */
    public function listAction()
    {


        $qb = $this->getDoctrine()
            ->getRepository('AppBundle:Alien')
            ->findAll();

        $response = $this->createApiResponse($qb);
        return $response;

    }

    /**
     * @Route("/api/search/{username}",name="api_aliens_search")
     * @Method("GET")
     */
    public function searchAction($username)
    {


        $aliens = $this->getDoctrine()
            ->getRepository('AppBundle:Alien')
            ->findSearch($username);
        if (!$aliens)
            throw $this->createNotFoundException('No alien found for username like '.$username);

        $response = $this->createApiResponse($aliens);
        return $response;

    }



    /**
     * @Route("/api/alien/{username}/friends" ,name="api_alien_friends")
     * @Method("GET")
     */
    public function listfriendsAction($username)
    {

        $alien = $this->getDoctrine()
            ->getRepository('AppBundle:Alien')
            ->findOneBy(array('username' => $username));
        if (!$alien)
            throw $this->createNotFoundException('No alien found for username '.$username);
        $friends = $alien->getFriends();
        $response  = $this->createApiResponse($friends);
        return $response;
    }

    /**
     * @Route("/api/addfriend",name="api_add_friend")
     * @Method("POST")
     */
    public function newfriendAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->request->replace($data);

        $alien = $this->getUser();

        if (!$alien)
            throw $this->createNotFoundException('No alien found for username '.$alien->getUsername());
        $friend =  $this->getDoctrine()
            ->getRepository('AppBundle:Alien')
            ->findOneBy(array('username' =>$request->request->get('username')));

        if (!$friend)
            throw $this->createNotFoundException('No alien found for username '.$request->request->get('username'));
        $em = $this->getDoctrine()->getManager();
        $alien->addFriends($friend);
        $em->persist($alien);
        $em->flush();
        
        return $this->getmyfriendAction();
    }

    /**
     * @Route("/api/myfriends", name="api_my_friend")
     * @Method("GET")
     */
    public function getmyfriendAction()
    {
        $alien = $this->getUser();

        if (!$alien)
            throw $this->createNotFoundException('No alien found for username '.$alien->getUsername());

        $friends = $alien->getFriends();
        $response = $this->createApiResponse($friends);
        return $response;
    }

    /**
     * @Route("api/removefriend/{id}",name="api_remove_friend")
     * @Method("DELETE")
     */
    public function removeFriend($id)
    {
        $alien = $this->getUser();
        if (!$alien)
            throw $this->createNotFoundException('No alien found for username '.$alien->getUsername());
        $em = $this->getDoctrine()->getManager();
        $friend = $em->getRepository('AppBundle:Alien')->find($id);
        if(!$friend)
        {
            throw $this->createNotFoundException('No friend found for id '.$id);
        }
        $alien->removeFriend($friend);
        $em->persist($alien);
        $em->flush();
        return $this->getmyfriendAction();
    }











}
