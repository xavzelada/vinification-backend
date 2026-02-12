<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CorsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $corsAllowOriginRegex, // inject from env
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Run late so we don't fight Nelmio if you still keep it
            KernelEvents::RESPONSE => ['onKernelResponse', -1000],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $origin = $request->headers->get('Origin');

        if (!$origin) {
            return;
        }

        // origin_regex=true style matching
        if (@preg_match('#' . $this->corsAllowOriginRegex . '#', '') === false) {
            // If your regex already includes ^...$, don't wrap it; just use it directly:
            $pattern = $this->corsAllowOriginRegex;
        } else {
            $pattern = $this->corsAllowOriginRegex;
        }

        if (!preg_match('#' . trim($pattern, '#') . '#', $origin)) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Vary', 'Origin'); // important for caches/CDNs
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
    }
}