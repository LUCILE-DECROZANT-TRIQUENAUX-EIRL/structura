<?php
/**
 * Displays the login message
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationSuccessHandler
extends DefaultAuthenticationSuccessHandler
implements AuthenticationSuccessHandlerInterface

{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * Constructor
     */
    public function __construct(HttpUtils $httpUtils, array $options, FlashBagInterface $flashBag)
    {
        parent::__construct($httpUtils, $options);

        // On récupère le service flashBag puisqu'on se trouve dans un service
        // et non dans un contrôleur
        $this->flashBag = $flashBag;
    }

    /**
     * Displays the login message
     * @return request, token
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // Récupération du nom de l'utilisateur
        $username = $token->getUsername();

        // Message affiché à l'utilisateur lorsqu'il se connecte avec succès
        $this->flashBag->add(
            'info', sprintf('Bienvenue %s.', $username)
        );

        // Comportement de la méthode du parent (DefaultAuthenticationSuccessHandler)
        return parent::onAuthenticationSuccess($request, $token);
    }
}
