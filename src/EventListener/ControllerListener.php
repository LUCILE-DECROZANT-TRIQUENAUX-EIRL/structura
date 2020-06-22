<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Entity\Association;

class ControllerListener
{

    private $session;
    private $em;

    public function __construct($session, $em)
    {
        $this->session = $session;
        $this->em = $em;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Check if the association data is filled, if not, add a flashbag
        $association = $this->em->getRepository(Association::class)->findAll();
        if (empty($association)) {
            $this->session->getFlashBag()->set(
                    'association-data-warning',
                    ''
            );
        }
    }

}
