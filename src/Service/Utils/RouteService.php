<?php
namespace App\Service\Utils;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Util class that provides methods to get more route infos.
 */
class RouteService
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
     * Class constructor with it's dependecy injections
     *
     * @param RouterInterface $router The router used in this class. This parameter is a dependency injection.
     * @param RequestStack $requestStack The request stack used in this class. This parameter is a dependency injection.
     */
    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
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