<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CronAuthenticationSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return ['kernel.request' => 'onKernelRequest'];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->isMainRequest()) {
            $headers = $event->getRequest()->headers;

            if ($this->isEligibleRoute($event)) {
                //Cron request
                if (!$this->isEligibleHeader($headers)) {
                    $response = new Response('', Response::HTTP_FORBIDDEN);
                    $event->setResponse($response);
                }
            }
        }
    }

    /**
     * Check if route is eligible
     *
     * @param RequestEvent $event
     *
     * @return boolean
     */
    private function isEligibleRoute(RequestEvent $event): bool
    {
        /** @var ParameterBag $attributes */
        $route = $event->getRequest()->get('_route');
        $eligibleRoutes = ['get_inhouse_guests', 'get_transactions','get_inhouse_no_virtual_rooms_guests'];

        return in_array($route, $eligibleRoutes) == TRUE;
    }

    /**
     * Check if header is eligible for Impala Authentication process
     *
     * @param HeaderBag $headers
     *
     * @return boolean
     */
    private function isEligibleHeader(HeaderBag $headers): bool
    {
        return $headers->get('X-Appengine-Cron');
    }
}