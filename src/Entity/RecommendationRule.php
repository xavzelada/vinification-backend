<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'recommendation_rules')]
#[ORM\HasLifecycleCallbacks]
class RecommendationRule
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Bodega::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Bodega $bodega;

    #[ORM\ManyToOne(targetEntity: Stage::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Stage $etapa;

    #[ORM\Column(type: 'string')]
    private string $nombre;

    #[ORM\Column(type: 'json')]
    private array $condiciones = [];

    #[ORM\Column(type: 'text')]
    private string $accionSugerida;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $producto = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $dosisSugerida = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $unidad = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $explicacion = null;

    #[ORM\Column(type: 'boolean')]
    private bool $activa = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBodega(): Bodega
    {
        return $this->bodega;
    }

    public function setBodega(Bodega $bodega): self
    {
        $this->bodega = $bodega;
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

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getCondiciones(): array
    {
        return $this->condiciones;
    }

    public function setCondiciones(array $condiciones): self
    {
        $this->condiciones = $condiciones;
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

    public function getExplicacion(): ?string
    {
        return $this->explicacion;
    }

    public function setExplicacion(?string $explicacion): self
    {
        $this->explicacion = $explicacion;
        return $this;
    }

    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function setActiva(bool $activa): self
    {
        $this->activa = $activa;
        return $this;
    }
}
