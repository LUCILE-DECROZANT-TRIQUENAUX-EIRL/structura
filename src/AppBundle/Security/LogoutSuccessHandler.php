<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class LogoutSuccessHandler
  extends DefaultLogoutSuccessHandler
  implements LogoutSuccessHandlerInterface

{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(HttpUtils $httpUtils, string $targetUrl = '/', FlashBagInterface $flashBag)
    {
        parent::__construct($httpUtils, $targetUrl);

        // On récupère le service flashBag puisqu'on se trouve dans un service
        // et non dans un contrôleur
        $this->flashBag = $flashBag;
    }

    public function onLogoutSuccess(Request $request)
    {
        // Message affiché à l'utilisateur lorsqu'il se déconnecte avec succès
        $this->flashBag->add(
            'info', sprintf('Vous vous êtes bien déconnecté.e.')
        );

        // Comportement de la méthode du parent (DefaultLogoutSuccessHandler)
        return parent::onLogoutSuccess($request);
    }
}