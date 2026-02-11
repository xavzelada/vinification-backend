<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use App\Enum\AlertStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'alertas')]
#[ORM\HasLifecycleCallbacks]
class Alert
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Batch::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Batch $lote;

    #[ORM\ManyToOne(targetEntity: AlertRule::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AlertRule $regla;

    #[ORM\Column(type: 'string')]
    private string $severidad;

    #[ORM\Column(type: 'string')]
    private string $estado = AlertStatus::OPEN;

    #[ORM\Column(type: 'text')]
    private string $mensaje;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4, nullable: true)]
    private ?string $valorDetectado = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4, nullable: true)]
    private ?string $tendencia = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $detectedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $ackAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $resolvedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLote(): Batch
    {
        return $this->lote;
    }

    public function setLote(Batch $lote): self
    {
        $this->lote = $lote;
        return $this;
    }

    public function getRegla(): AlertRule
    {
        return $this->regla;
    }

    public function setRegla(AlertRule $regla): self
    {
        $this->regla = $regla;
        return $this;
    }

    public function getSeveridad(): string
    {
        return $this->severidad;
    }

    public function setSeveridad(string $severidad): self
    {
        $this->severidad = $severidad;
        return $this;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;
        return $this;
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): self
    {
        $this->mensaje = $mensaje;
        return $this;
    }

    public function getValorDetectado(): ?string
    {
        return $this->valorDetectado;
    }

    public function setValorDetectado(?string $valorDetectado): self
    {
        $this->valorDetectado = $valorDetectado;
        return $this;
    }

    public function getTendencia(): ?string
    {
        return $this->tendencia;
    }

    public function setTendencia(?string $tendencia): self
    {
        $this->tendencia = $tendencia;
        return $this;
    }

    public function getDetectedAt(): \DateTimeImmutable
    {
        return $this->detectedAt;
    }

    public function setDetectedAt(\DateTimeImmutable $detectedAt): self
    {
        $this->detectedAt = $detectedAt;
        return $this;
    }

    public function getAckAt(): ?\DateTimeImmutable
    {
        return $this->ackAt;
    }

    public function setAckAt(?\DateTimeImmutable $ackAt): self
    {
        $this->ackAt = $ackAt;
        return $this;
    }

    public function getResolvedAt(): ?\DateTimeImmutable
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(?\DateTimeImmutable $resolvedAt): self
    {
        $this->resolvedAt = $resolvedAt;
        return $this;
    }
}
