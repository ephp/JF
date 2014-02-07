<?php

namespace JF\ACLBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class LocaleListener implements EventSubscriberInterface {

    private $defaultLocale;

    /**
     * @var SecurityContextInterface 
     */
    private $sc = null;

    /**
     * @var \Ephp\ACLBundle\Model\BaseUser 
     */
    private $user;

    public function __construct(SecurityContextInterface $sc, $defaultLocale = 'en') {
        $this->defaultLocale = $defaultLocale;
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
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $request->setLocale($this->user->getLocale());
    }

    public static function getSubscribedEvents() {
        return array(
            // deve essere registrato prima dell'ascoltatore predefinito di locale
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }

}
