<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
        } elseif ($exception instanceof AccessDeniedException) {
            $status = 403;
        } elseif ($exception instanceof AuthenticationException) {
            $status = 401;
        } else {
            $status = 500;
        }

        $message = $exception->getMessage() ?: 'Error interno';
        $details = null;

        if ($status === 400 && str_contains($message, ':')) {
            $details = array_map('trim', explode(';', $message));
        }

        $payload = [
            'error' => [
                'message' => $message,
                'code' => $status,
                'details' => $details
            ]
        ];

        $event->setResponse(new JsonResponse($payload, $status));
    }
}
