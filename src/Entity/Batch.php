<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use App\Enum\BatchStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lotes')]
#[ORM\HasLifecycleCallbacks]
class Batch
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $codigo;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $volumenLitros;

    #[ORM\Column(type: 'string')]
    private string $variedad;

    #[ORM\Column(type: 'integer')]
    private int $cosechaYear;

    #[ORM\Column(type: 'string')]
    private string $estado = BatchStatus::ACTIVE;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $fechaInicio = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $fechaEmbotellado = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $regulacion = null;

    #[ORM\ManyToOne(targetEntity: Bodega::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Bodega $bodega;

    #[ORM\ManyToOne(targetEntity: Stage::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Stage $etapa;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    private ?Location $ubicacion = null;

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

    public function getVolumenLitros(): string
    {
        return $this->volumenLitros;
    }

    public function setVolumenLitros(string $volumenLitros): self
    {
        $this->volumenLitros = $volumenLitros;
        return $this;
    }

    public function getVariedad(): string
    {
        return $this->variedad;
    }

    public function setVariedad(string $variedad): self
    {
        $this->variedad = $variedad;
        return $this;
    }

    public function getCosechaYear(): int
    {
        return $this->cosechaYear;
    }

    public function setCosechaYear(int $cosechaYear): self
    {
        $this->cosechaYear = $cosechaYear;
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

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(?\DateTimeInterface $fechaInicio): self
    {
        $this->fechaInicio = $fechaInicio;
        return $this;
    }

    public function getFechaEmbotellado(): ?\DateTimeInterface
    {
        return $this->fechaEmbotellado;
    }

    public function setFechaEmbotellado(?\DateTimeInterface $fechaEmbotellado): self
    {
        $this->fechaEmbotellado = $fechaEmbotellado;
        return $this;
    }

    public function getRegulacion(): ?array
    {
        return $this->regulacion;
    }

    public function setRegulacion(?array $regulacion): self
    {
        $this->regulacion = $regulacion;
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

    public function getEtapa(): Stage
    {
        return $this->etapa;
    }

    public function setEtapa(Stage $etapa): self
    {
        $this->etapa = $etapa;
        return $this;
    }

    public function getUbicacion(): ?Location
    {
        return $this->ubicacion;
    }

    public function setUbicacion(?Location $ubicacion): self
    {
        $this->ubicacion = $ubicacion;
        return $this;
    }
}
