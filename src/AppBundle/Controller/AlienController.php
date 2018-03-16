<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AlienController extends Controller
{
    /**
     * @Route("/aliens",name="aliens_show")
     */
    public function showAllAlienAction()
    {
        $em = $this->getDoctrine()->getManager();

        $aliens  = $em->getRepository('AppBundle:Alien')
            ->findAll();

        return $this->render('alien/showAll.html.twig', array('aliens' => $aliens));
    }

    /**
     * @Route("/addfriend/{id}", name="add_friend")
     */
    public function addFriendAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Alien
         */
        $alien = $this->getUser();
        if($alien)
        {
            $friend = $em->getRepository('AppBundle:Alien')->find($id);
            if(!$friend)
            {
                throw $this->createNotFoundException('No friend found for id '.$id);
            }
            $alien->addFriends($friend);
            $em->persist($alien);
            $em->flush();

            return $this->redirectToRoute('list_friend',array("id" =>$alien->getId()));
        }

        return $this->redirectToRoute('aliens_show');

    }

    /**
     * @Route("/listfriend/{id}",name="list_friend")
     */
    public function listFriends($id)
    {
        $em = $this->getDoctrine()->getManager();
        $alien = $em->getRepository('AppBundle:Alien')->find($id);
        $friends = $alien->getFriends();
        return $this->render('alien/listfriends.html.twig',array('friends'=>$friends));
    }

    /**
     * @Route("/removefriend/{id}" , name="remove_friend")
     */
    public function removeFriends($id)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Alien
         */
        $alien = $this->getUser();
        if($alien)
        {
            $friend = $em->getRepository('AppBundle:Alien')->find($id);
            if(!$friend)
            {
                throw $this->createNotFoundException('No friend found for id '.$id);
            }
            $alien->removeFriend($friend);
            $em->persist($alien);
            $em->flush();

            return $this->redirectToRoute('list_friend',array("id" =>$alien->getId()));
        }

        return $this->redirectToRoute('aliens_show');
    }
}
