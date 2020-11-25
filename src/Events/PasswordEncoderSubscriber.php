<?php 

namespace App\Events;


use App\Entity\User;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class PasswordEncoderSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ "encodePassword", EventPriorities::PRE_WRITE ]
        ];

    }
    /**
     * Password Encoder
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder; 

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }



    public function encodePassword(ViewEvent $event)
    {
      
        $result = $event->getControllerResult();
       
        $method = $event->getRequest()->getMethod(); // POST, PUT, DELETE, ...
        if($result instanceof User && $method === "POST"){
            $hash = $this->encoder->encodePassword($result, $result->getPassword());
            $result->setPassword($hash);
        }
       
    }

}