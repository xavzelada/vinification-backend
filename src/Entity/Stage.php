<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'etapas')]
#[ORM\HasLifecycleCallbacks]
class Stage
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $nombre;

    #[ORM\Column(type: 'integer')]
    private int $orden;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\ManyToOne(targetEntity: Bodega::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Bodega $bodega;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOrden(): int
    {
        return $this->orden;
    }

    public function setOrden(int $orden): self
    {
        $this->orden = $orden;
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

    public function getBodega(): Bodega
    {
        return $this->bodega;
    }

    public function setBodega(Bodega $bodega): self
    {
        $this->bodega = $bodega;
        return $this;
    }
}
