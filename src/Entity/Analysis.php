<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'analisis')]
#[ORM\HasLifecycleCallbacks]
class Analysis
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Batch::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Batch $lote;

    #[ORM\ManyToOne(targetEntity: AnalysisType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AnalysisType $tipo;

    #[ORM\Column(type: 'string')]
    private string $unidad;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4)]
    private string $valor;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $metodo = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $laboratorio = null;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $fechaMuestra;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $fechaResultado = null;

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

    public function getTipo(): AnalysisType
    {
        return $this->tipo;
    }

    public function setTipo(AnalysisType $tipo): self
    {
        $this->tipo = $tipo;
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

    public function getValor(): string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;
        return $this;
    }

    public function getMetodo(): ?string
    {
        return $this->metodo;
    }

    public function setMetodo(?string $metodo): self
    {
        $this->metodo = $metodo;
        return $this;
    }

    public function getLaboratorio(): ?string
    {
        return $this->laboratorio;
    }

    public function setLaboratorio(?string $laboratorio): self
    {
        $this->laboratorio = $laboratorio;
        return $this;
    }

    public function getFechaMuestra(): \DateTimeInterface
    {
        return $this->fechaMuestra;
    }

    public function setFechaMuestra(\DateTimeInterface $fechaMuestra): self
    {
        $this->fechaMuestra = $fechaMuestra;
        return $this;
    }

    public function getFechaResultado(): ?\DateTimeInterface
    {
        return $this->fechaResultado;
    }

    public function setFechaResultado(?\DateTimeInterface $fechaResultado): self
    {
        $this->fechaResultado = $fechaResultado;
        return $this;
    }
}
