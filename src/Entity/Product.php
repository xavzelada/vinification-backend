<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'productos')]
#[ORM\HasLifecycleCallbacks]
class Product
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Bodega::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Bodega $bodega;

    #[ORM\Column(type: 'string')]
    private string $nombre;

    #[ORM\Column(type: 'string')]
    private string $categoria;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'string')]
    private string $unidad;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $rangoDosisMin = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $rangoDosisMax = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notas = null;

    #[ORM\Column(type: 'boolean')]
    private bool $activo = true;

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

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
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

    public function getRangoDosisMin(): ?string
    {
        return $this->rangoDosisMin;
    }

    public function setRangoDosisMin(?string $rangoDosisMin): self
    {
        $this->rangoDosisMin = $rangoDosisMin;
        return $this;
    }

    public function getRangoDosisMax(): ?string
    {
        return $this->rangoDosisMax;
    }

    public function setRangoDosisMax(?string $rangoDosisMax): self
    {
        $this->rangoDosisMax = $rangoDosisMax;
        return $this;
    }

    public function getNotas(): ?string
    {
        return $this->notas;
    }

    public function setNotas(?string $notas): self
    {
        $this->notas = $notas;
        return $this;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;
        return $this;
    }
}
