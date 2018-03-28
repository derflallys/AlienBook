<?php

namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use AppBundle\Entity\Alien;
use AppBundle\Form\RegistrationFormType;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{

    /**
     * @Route("/pai/register",name="api_alien_register")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->request->replace($data);

        $alien = new Alien();

        $form = $this->createForm(RegistrationFormType::class,$alien);
        $this->processForm($request,$form);
        if(!$form->isValid())
        {
            return $this->throwApiProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($alien);
        $em->flush();

        $location = $this->generateUrl('api_alien_show',[
            'username' =>$alien->getUsername()
        ]);

        $response = $this->createApiResponse($alien,201);
        $response->headers->set('Location',$location);

        return $response;
    }

    protected function createApiResponse($data,$statusCode= 200)
    {
        $json = $this->serialize($data);
        return new Response($json,$statusCode,[
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*'
        ]);
    }

    protected function serialize($data)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $request = $this->get('request_stack')->getCurrentRequest();
        $group = array('Default');
        if($request->query->get('deep')){
            $group[] = 'deep';
        }
        $context->setGroups($group);
        return $this->container->get('jms_serializer')
            ->serialize($data,'json',$context);
    }

    protected function processForm(Request $request,FormInterface $formInteface)
    {
        $body = $request->getContent();
        $data = json_decode($body,true);
        if(null=== $data)
        {
            $apiProblem = new ApiProblem(400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);

            throw new ApiProblemException($apiProblem);

        }
        $clearMissing = $request->getMethod() !='PATCH';

        $formInteface->submit($data,$clearMissing);
    }

    protected function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);

        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR);
        $apiProblem->set('errors',$errors);
        throw new ApiProblemException($apiProblem);
    }

    protected function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();

        }


        foreach ($form->all() as $childForm) {

            if ($childForm instanceof FormInterface) {

                if ($childErrors = $this->getErrorsFromForm($childForm)) {

                    $errors[$childForm->getName()] = $childErrors;

                }

            }

        }


        return $errors;
    }
}
