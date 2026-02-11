<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use App\Enum\ActionStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'acciones')]
#[ORM\HasLifecycleCallbacks]
class ActionApplied
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Batch::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Batch $lote;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $producto;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $operador;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fecha;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $dosis;

    #[ORM\Column(type: 'string')]
    private string $unidad;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $objetivo = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observaciones = null;

    #[ORM\Column(type: 'string')]
    private string $estado = ActionStatus::PENDING;

    #[ORM\ManyToOne(targetEntity: Stage::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Stage $etapa;

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

    public function getProducto(): Product
    {
        return $this->producto;
    }

    public function setProducto(Product $producto): self
    {
        $this->producto = $producto;
        return $this;
    }

    public function getOperador(): User
    {
        return $this->operador;
    }

    public function setOperador(User $operador): self
    {
        $this->operador = $operador;
        return $this;
    }

    public function getFecha(): \DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getDosis(): string
    {
        return $this->dosis;
    }

    public function setDosis(string $dosis): self
    {
        $this->dosis = $dosis;
        return $this;
    }

    public function getUnidad(): string
    {
        return $this->unidad;
    }

    public function setUnidad(string $unidad): self
    {
        $this->unidad = $unidad;
        return $this;
    }

    public function getObjetivo(): ?string
    {
        return $this->objetivo;
    }

    public function setObjetivo(?string $objetivo): self
    {
        $this->objetivo = $objetivo;
        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;
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

    public function getEtapa(): Stage
    {
        return $this->etapa;
    }

    public function setEtapa(Stage $etapa): self
    {
        $this->etapa = $etapa;
        return $this;
    }
}
