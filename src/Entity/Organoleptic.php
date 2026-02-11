<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'organolepticas')]
#[ORM\HasLifecycleCallbacks]
class Organoleptic
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

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fecha;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $nariz = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $boca = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $color = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $defectos = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $intensidad = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notasLibres = null;

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

    public function getFecha(): \DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getNariz(): ?array
    {
        return $this->nariz;
    }

    public function setNariz(?array $nariz): self
    {
        $this->nariz = $nariz;
        return $this;
    }

    public function getBoca(): ?array
    {
        return $this->boca;
    }

    public function setBoca(?array $boca): self
    {
        $this->boca = $boca;
        return $this;
    }

    public function getColor(): ?array
    {
        return $this->color;
    }

    public function setColor(?array $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getDefectos(): ?array
    {
        return $this->defectos;
    }

    public function setDefectos(?array $defectos): self
    {
        $this->defectos = $defectos;
        return $this;
    }

    public function getIntensidad(): ?string
    {
        return $this->intensidad;
    }

    public function setIntensidad(?string $intensidad): self
    {
        $this->intensidad = $intensidad;
        return $this;
    }

    public function getNotasLibres(): ?string
    {
        return $this->notasLibres;
    }

    public function setNotasLibres(?string $notasLibres): self
    {
        $this->notasLibres = $notasLibres;
        return $this;
    }
}
