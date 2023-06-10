<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\User;
use App\Entity\Responsibility;
use App\Exception\FormIsNotSubmitted;
use App\Exception\FormIsInvalid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class containing methods used to display user's related pages
 */
class UserService
{
    /**
     * @var RouterInterface $router
     */
    private $router;

    /**
     * @var RequestStack $requestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var UserPasswordHasherInterface $em
     */
    private $passwordHasher;

    /**
     * Class constructor with it's dependecy injections
     *
     * @param RouterInterface $router The router used in this class. This parameter is a dependency injection.
     * @param RequestStack $requestStack The request stack used in this class. This parameter is a dependency injection.
     */
    public function __construct(
            EntityManagerInterface $em,
            RouterInterface $router,
            RequestStack $requestStack,
            UserPasswordHasherInterface $passwordHasher
        )
    {
        $this->em = $em;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Create a user from a form and save it into the database
     *
     * @param type $createdUser User to create
     * @param type $form
     * @return type
     * @throws FormIsNotSubmitted
     * @throws FormIsInvalid
     */
    public function createUser($userWaitingCreation, $form)
    {
        if (!$form->isSubmitted())
        {
            throw new FormIsNotSubmitted('This method should only be called when the form is submitted');
        }
        elseif (!$form->isValid())
        {
            throw new FormIsInvalid(sprintf(
                    'Form is invalid: %s',
                    $form->getErrors()
            ));
        }
        else
        {
            $user = new User();
            $password = $this->passwordHasher->hashPassword(
                    $userWaitingCreation,
                    $userWaitingCreation->getPlainPassword()
            );
            $userWaitingCreation->setPassword($password);

            $userWaitingCreation = $this->manageResponsibilities($userWaitingCreation);

            $this->em->persist($userWaitingCreation);
            $this->em->flush();

            return $userWaitingCreation;
        }

    }

    /**
     * Create the very first admin account of Structura
     *
     * @param string $username Username of the admin account
     * @param string $plainPassword Password of the admin account
     *
     * @return User Admin account created
     *
     * @throws Exception Admin account already exists
     */
    public function createFirstAdminAccount(string $username, string $plainPassword)
    {
        $existingUsers = $this->em->getRepository(User::class)->findAll();
        if (count($existingUsers) > 0)
        {
            throw new \Exception('An admin account is already created.');
        }

        // Setup username
        $user = new User();
        $user->setUsername($username);

        // Setup password
        $password = $this->passwordHasher->hashPassword(
                $user,
                $plainPassword
        );
        $user->setPassword($password);

        // Setup responsibilities
        $adminResponsibility = $this->em->getRepository(Responsibility::class)->findOneBy([
            'label' => Responsibility::ADMINISTRATEURICE_LABEL,
        ]);
        $user->addResponsibility($adminResponsibility);
        $adminSensibleResponsibility = $this->em->getRepository(Responsibility::class)->findOneBy([
            'label' => Responsibility::ADMINISTRATEURICE_SENSIBLE_LABEL,
        ]);
        $user->addResponsibility($adminSensibleResponsibility);
        $user = $this->manageResponsibilities($user);

        // Save account
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Add or remove automatically user's responsibilities
     * depending on which one they already have
     *
     * @param User $user user that needs to have responsibilities updated
     * @return User user with its responsibilities updated
     */
    public function manageResponsibilities($user)
    {
        // by default, add the registered responsibility
        $registeredResponsibility = $this->em->getRepository(Responsibility::class)->findOneBy([
            'label' => Responsibility::REGISTERED_LABEL,
        ]);
        $user->addResponsibility($registeredResponsibility);

        // remove responsibilities that conflict themselves
        $sympathizeResponsibility = $this->em->getRepository(Responsibility::class)->findOneBy([
            'label' => Responsibility::SYMPATHIZE_LABEL,
        ]);
        $memberResponsibility = $this->em->getRepository(Responsibility::class)->findOneBy([
            'label' => Responsibility::MEMBER,
        ]);
        $exMemberResponsibility = $this->em->getRepository(Responsibility::class)->findOneBy([
            'label' => Responsibility::EX_MEMBER,
        ]);
        if ($user->hasResponsibility($sympathizeResponsibility))
        {
            $user->removeResponsibility($memberResponsibility);
        }
        else if ($user->hasResponsibility($memberResponsibility))
        {
            $user->removeResponsibility($exMemberResponsibility);
        }

        return $user;
    }

    /**
     * Return the route name given an url e.g. : "/people/edit/1" is "people_edit".
     *
     * @param string $url The url you want to find the associated route.
     *
     * @return string The corresponding route name.
     */
    public function getRouteNameFromUrl(string $url)
    {
        return $this->router->match($url)['_route'];
    }

    /**
     * Give more informations about the previous Url. Here is a list of the diffents keys in the return array :
     *
     * "_controller" is the full controller name and action, e.g. : "MyBundle\Controller\MyController::myAction"
     *
     * "_route" is the previous route name corresponding to the previous route url.
     *
     * "_methods" is a string array containing the names of all availables methods for the given route.
     * Note that we can't determine which one was used for the previous route.
     *
     * Also, if there is any parameters in the url, they will be in the array, e.g. : ["param_name"] => "param_value"
     *
     * @return array The informations about the previous url.
     */
    public function getPreviousRouteInfo()
    {
        // Getting the current request
        $request = $this->requestStack->getCurrentRequest();

        // Getting the context from the current request
        $context = new RequestContext();
        $context->fromRequest($request);
        $this->router->setContext($context);

        // Grabing some usefull vars
        $referer = $request->headers->get('referer');
        $host    = $request->headers->get('host');

        // String treatment to get the previous route url from the referer.
        $routeStartindex = strpos($referer, $host) + strlen($host);
        $previousRouteUrl = substr($referer, $routeStartindex);

        $infos = $this->router->match($previousRouteUrl);
        $infos['_methods'] = $this->getAllAvailableMethodsForRoute($previousRouteUrl);

        return $infos;
    }

    /**
     * Returns all the http methods that are available for a given route.
     *
     * @param string $routeUrl The route you want to know which methods are available.
     *
     * @return string[] A string array containing the names of the available methods.
     */
    public function getAllAvailableMethodsForRoute(string $routeUrl)
    {
        // Var initialisation
        $httpMethods = ['HEAD', 'GET', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE'];
        $availableMethods = [];

        // Getting the current request
        $request = $this->requestStack->getCurrentRequest();

        // Getting the context from the current request
        $context = new RequestContext();
        $context->fromRequest($request);
        $this->router->setContext($context);

        // Save current http method
        $currentMethod = $context->getMethod();

        foreach($httpMethods as $method)
        {
            // We try to find if there is an existing route
            // For the current httpMethod ($method)
            try
            {
                $context->setMethod($method);
                $params = $this->router->match($routeUrl);
                $availableMethods[] = $method;
            }
            catch(ResourceNotFoundException | MethodNotAllowedException $e)
            {
                // No route matched for this method, we do nothing
                // And hop to the next method
            }
        }

        // Set back original http method
        $context->setMethod($currentMethod);

        return $availableMethods;
    }
}