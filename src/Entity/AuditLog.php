<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'audit_logs')]
#[ORM\HasLifecycleCallbacks]
class AuditLog
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $entity;

    #[ORM\Column(type: 'string')]
    private string $entityId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $userId = null;

    #[ORM\Column(type: 'string')]
    private string $action;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $before = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $after = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $ip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getBefore(): ?array
    {
        return $this->before;
    }

    public function setBefore(?array $before): self
    {
        $this->before = $before;
        return $this;
    }

    public function getAfter(): ?array
    {
        return $this->after;
    }

    public function setAfter(?array $after): self
    {
        $this->after = $after;
        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }
}
