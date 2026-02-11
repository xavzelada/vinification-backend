<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'alert_rules')]
#[ORM\HasLifecycleCallbacks]
class AlertRule
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

    #[ORM\Column(type: 'string')]
    private string $parametro;

    #[ORM\Column(type: 'string')]
    private string $operador;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4, nullable: true)]
    private ?string $valor = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4, nullable: true)]
    private ?string $valorMax = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $periodoDias = null;

    #[ORM\Column(type: 'string')]
    private string $severidad;

    #[ORM\Column(type: 'boolean')]
    private bool $activa = true;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

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

    public function getParametro(): string
    {
        return $this->parametro;
    }

    public function setParametro(string $parametro): self
    {
        $this->parametro = $parametro;
        return $this;
    }

    public function getOperador(): string
    {
        return $this->operador;
    }

    public function setOperador(string $operador): self
    {
        $this->operador = $operador;
        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(?string $valor): self
    {
        $this->valor = $valor;
        return $this;
    }

    public function getValorMax(): ?string
    {
        return $this->valorMax;
    }

    public function setValorMax(?string $valorMax): self
    {
        $this->valorMax = $valorMax;
        return $this;
    }

    public function getPeriodoDias(): ?int
    {
        return $this->periodoDias;
    }

    public function setPeriodoDias(?int $periodoDias): self
    {
        $this->periodoDias = $periodoDias;
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

    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function setActiva(bool $activa): self
    {
        $this->activa = $activa;
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
}
