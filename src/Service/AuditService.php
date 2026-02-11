<?php

namespace App\Service;

use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;

class AuditService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function log(string $entity, string $entityId, string $action, ?array $before, ?array $after, ?int $userId = null, ?string $ip = null): void
    {
        $log = new AuditLog();
        $log->setEntity($entity)
            ->setEntityId($entityId)
            ->setAction($action)
            ->setBefore($before)
            ->setAfter($after)
            ->setUserId($userId)
            ->setIp($ip);
        $this->em->persist($log);
        $this->em->flush();
    }
}
