<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class ControllerListener
{
    public function __construct(private AssociationRepository $associationRepository)
    {
    }

    public function onKernelController(ControllerEvent $event)
    {
        /** @var Session $session */
        $session = $event->getRequest()->getSession();

        // Check if the association data is filled, if not, add a flashbag
        $association = $this->associationRepository->findAll();
        if (empty($association)) {
            $session->getFlashBag()->set(
                    'association-data-warning',
                    ''
            );
        }
    }
}
