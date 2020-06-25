<?php
/**
 * Displays the logout message
 */

namespace App\Security;

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

    /**
     * Constructor
     */
    public function __construct(HttpUtils $httpUtils, string $targetUrl = 'home', FlashBagInterface $flashBag)
    {
        parent::__construct($httpUtils, $targetUrl);

        // Get flashbag service
        $this->flashBag = $flashBag;
    }

    /**
     * Displays a message on logout
     * @return request
     */
    public function onLogoutSuccess(Request $request)
    {
        // Message displayed to the user
        $this->flashBag->add(
            'info', sprintf('Vous vous êtes bien déconnecté.e.')
        );

        // Parent method comportement
        return parent::onLogoutSuccess($request);
    }
}
