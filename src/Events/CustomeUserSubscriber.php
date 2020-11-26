<?php

namespace App\Events;


use App\Entity\User;
use App\Entity\Customer;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomeUserSubscriber implements EventSubscriberInterface

{
    /**
     *
     * @var Security
     */
    private $security;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ["setUserForCustomer", EventPriorities::PRE_VALIDATE]
        ];
    }

    public function __construct(Security $security){
        $this->security = $security;
    }


    public function setUserForCustomer(ViewEvent $event)
    {
        $user = new User();

        $customer  =  $event->getControllerResult();


        $method = $event->getRequest()->getMethod(); // POST, PUT, DELETE, ...

        if ($customer instanceof Customer && $method === "POST") {
                $user = $this->security->getUser();
                $customer->setUser($user);
         }
    }
}