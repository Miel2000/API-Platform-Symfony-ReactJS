<?php

namespace App\Events;



use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoiceChronoSubscriber implements EventSubscriberInterface

{
    /**
     *
     * @var Security
     */
    private $security;

    /**
     *
     * @var InvoiceRepository
     */
    private $repository;

    public function __construct(Security $security, InvoiceRepository $repository )
    {
        $this->security = $security;
        $this->repository = $repository;
    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ["setChronoForInvoice", EventPriorities::PRE_VALIDATE]
        ];
    }

    public function setChronoForInvoice(ViewEvent $event)
    {
        $invoice  =  $event->getControllerResult();
        

        $method = $event->getRequest()->getMethod(); // POST, PUT, DELETE, ...

        if ($invoice instanceof Invoice && $method === "POST") {

        // 1. trouve l'utilisateur actuellement connecté (Security)
            $user = $this->security->getUser();
            
        // 2. need le repository des invoices (InvoiceRepository)
            $nextChrono = $this->repository->findNextChrono($user);

        // 3. chope la derniere facture, ajouter +1 à son chrono
            $invoice->setChrono($nextChrono);


            // Si la date n'est pas présente, alors on l'ajoute à la date actuelle.
            if  (empty( $invoice->getSendAt() ) ){
                $invoice->setSendAt(new \DateTime());
            }
            
        }
    }
}