<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class StickySessionResponseListener
{
    private const TIMESTAMP_2200_JANUARY = 7258118400;
    /**
     * @var StickySessionControllerArgumentsListener
     */
    private $controllerArgumentsListener;

    public function __construct(StickySessionControllerArgumentsListener $controllerArgumentsListener)
    {
        $this->controllerArgumentsListener = $controllerArgumentsListener;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $sessionKey = $this->controllerArgumentsListener->getSessionKey();
        $stickyCookie = $event->getRequest()->cookies->get($sessionKey);
        $headers = $event->getResponse()->headers;
        $headers->setCookie(Cookie::create($sessionKey, $stickyCookie, self::TIMESTAMP_2200_JANUARY));
        $event->getResponse()->headers = $headers;
    }
}