<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'mediciones')]
#[ORM\HasLifecycleCallbacks]
class Measurement
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Batch::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Batch $lote;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $usuario;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $fechaHora;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 4)]
    private string $densidad;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    private string $temperaturaC;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?string $brix = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comentario = null;

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

    public function getUsuario(): User
    {
        return $this->usuario;
    }

    public function setUsuario(User $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getFechaHora(): \DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeImmutable $fechaHora): self
    {
        $this->fechaHora = $fechaHora;
        return $this;
    }

    public function getDensidad(): string
    {
        return $this->densidad;
    }

    public function setDensidad(string $densidad): self
    {
        $this->densidad = $densidad;
        return $this;
    }

    public function getTemperaturaC(): string
    {
        return $this->temperaturaC;
    }

    public function setTemperaturaC(string $temperaturaC): self
    {
        $this->temperaturaC = $temperaturaC;
        return $this;
    }

    public function getBrix(): ?string
    {
        return $this->brix;
    }

    public function setBrix(?string $brix): self
    {
        $this->brix = $brix;
        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;
        return $this;
    }
}
