<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16/03/18
 * Time: 01:21
 */

namespace AppBundle\EventListener;


use Composer\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class RedirectAfterRegistrationSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;
    private $router;
    public function __construct(RouterInterface $router)
    {

        $this->router = $router;
    }
    public function onRegistrationSuccess(FormEvent $event)
    {
        $url = $this->getTargetPath($event->getRequest()->getSession(), 'main');
        if(!$url)
        {
            $url = $this->router->generate('homepage');
        }
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
       return [
           FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
       ];
    }
}