<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends BaseController
{
    /**
     * @Route("/api/login",name="api_login")
     * @Method("POST")
     */
    public function loginAction(Request $request)
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        $alien = $this->getDoctrine()
            ->getRepository('AppBundle:Alien')
            ->findOneBy(array('username' => $username));
        if(!$alien)
        {
            throw $this->createNotFoundException('No alien found for username '.$username);
        }
        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($alien,$password);
        if(!$isValid)
        {
            throw new BadCredentialsException();
        }

        $token = $this->get('lexik_jwt_authentication.encoder.default')
            ->encode([
                'username' => $username
            ]);
        $data = [
            'token' => $token,
            'alien' => $alien
        ];
        return $this->createApiResponse($data,201);

    }
}
