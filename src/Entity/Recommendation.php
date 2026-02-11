<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'recomendaciones')]
#[ORM\HasLifecycleCallbacks]
class Recommendation
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Batch::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Batch $lote;

    #[ORM\ManyToOne(targetEntity: Stage::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Stage $etapa;

    #[ORM\Column(type: 'json')]
    private array $entradas = [];

    #[ORM\Column(type: 'text')]
    private string $accionSugerida;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $producto = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $dosisSugerida = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $unidad = null;

    #[ORM\Column(type: 'text')]
    private string $explicacion;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $confidence = null;

    #[ORM\Column(type: 'string')]
    private string $estado = 'sugerida';

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

    public function getEtapa(): Stage
    {
        return $this->etapa;
    }

    public function setEtapa(Stage $etapa): self
    {
        $this->etapa = $etapa;
        return $this;
    }

    public function getEntradas(): array
    {
        return $this->entradas;
    }

    public function setEntradas(array $entradas): self
    {
        $this->entradas = $entradas;
        return $this;
    }

    public function getAccionSugerida(): string
    {
        return $this->accionSugerida;
    }

    public function setAccionSugerida(string $accionSugerida): self
    {
        $this->accionSugerida = $accionSugerida;
        return $this;
    }

    public function getProducto(): ?Product
    {
        return $this->producto;
    }

    public function setProducto(?Product $producto): self
    {
        $this->producto = $producto;
        return $this;
    }

    public function getDosisSugerida(): ?string
    {
        return $this->dosisSugerida;
    }

    public function setDosisSugerida(?string $dosisSugerida): self
    {
        $this->dosisSugerida = $dosisSugerida;
        return $this;
    }

    public function getUnidad(): ?string
    {
        return $this->unidad;
    }

    public function setUnidad(?string $unidad): self
    {
        $this->unidad = $unidad;
        return $this;
    }

    public function getExplicacion(): string
    {
        return $this->explicacion;
    }

    public function setExplicacion(string $explicacion): self
    {
        $this->explicacion = $explicacion;
        return $this;
    }

    public function getConfidence(): ?string
    {
        return $this->confidence;
    }

    public function setConfidence(?string $confidence): self
    {
        $this->confidence = $confidence;
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
}
