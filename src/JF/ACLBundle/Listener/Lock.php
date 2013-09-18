<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JF\ACLBundle\Listener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Generator Manager service
 * 
 * @author David Epely <depely@prestaconcept.net>
 * @author Christophe Dolivet
 */
class Lock {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var \appDevDebugProjectContainer 
     */
    private $container;

    /**
     * @var GetResponseEvent 
     */
    private $event = null;

    /**
     * @var Request 
     */
    private $request = null;

    /**
     * @var SecurityContextInterface 
     */
    private $sc = null;

    /**
     * @var \Ephp\ACLBundle\Model\BaseUser 
     */
    private $user;

    public function __construct($router, $container, SecurityContextInterface $sc) {
        $this->router = $router;
        $this->container = $container;
        $this->sc = $sc;
        if ($this->sc) {
            if (null !== $token = $this->sc->getToken()) {
                if (is_object($user = $token->getUser())) {
                    $this->user = $user;
                }
            }
        }
    }

    public function onKernelRequest(FilterControllerEvent $event) {
        if ($this->user && $this->user->getCliente() && $this->user->getCliente()->getBloccato()) {
            $this->event = $event;
            $this->request = $event->getRequest();
            $rc = $this->router->getRouteCollection();
            /* @var $rc \Symfony\Component\Routing\RouteCollection */

            $route = $rc->get($this->request->get('_route'));
            if (!$route)
                return false;
            $unlock = $route->getOption('unlock');
            try {
                // Verifico che sia stata richiesta la memorizzazione delle statistiche
                if (!$unlock) {
                    if (in_array($this->request->get('_route'), array(
                                'fos_user_security_login', 'fos_user_security_check',
                                'fos_user_security_logout', '_security_logout',
                                'fos_user_profile_show',
                                'fos_js_routing_js',
                                'jf_core_render_menu',
                                '_wdt', '_internal',
                            ))) {
                        throw new \Exception('OK');
                    }
                    if (strpos($this->request->get('_route'), '_profiler') !== false) {
                        throw new \Exception('OK');
                    }
                    if (strpos($this->request->get('_route'), '_assetic') !== false) {
                        throw new \Exception('OK');
                    }
                }
                throw new NotFoundHttpException('Utenza bloccata');
            } catch (NotFoundHttpException $e) {
                throw $e;
            } catch (\Exception $e) { }
        }
    }

}
