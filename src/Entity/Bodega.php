<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bodegas')]
#[ORM\HasLifecycleCallbacks]
class Bodega
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $codigo;

    #[ORM\Column(type: 'string')]
    private string $nombre;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $pais = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;
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

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(?string $pais): self
    {
        $this->pais = $pais;
        return $this;
    }
}
