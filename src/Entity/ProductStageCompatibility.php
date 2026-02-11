<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_stage_compat')]
class ProductStageCompatibility
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $producto;

    #[ORM\ManyToOne(targetEntity: Stage::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Stage $etapa;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $compatibilidad = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $dosisRecomendada = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $restricciones = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEtapa(): Stage
    {
        return $this->etapa;
    }

    public function setEtapa(Stage $etapa): self
    {
        $this->etapa = $etapa;
        return $this;
    }

    public function getCompatibilidad(): ?string
    {
        return $this->compatibilidad;
    }

    public function setCompatibilidad(?string $compatibilidad): self
    {
        $this->compatibilidad = $compatibilidad;
        return $this;
    }

    public function getDosisRecomendada(): ?string
    {
        return $this->dosisRecomendada;
    }

    public function setDosisRecomendada(?string $dosisRecomendada): self
    {
        $this->dosisRecomendada = $dosisRecomendada;
        return $this;
    }

    public function getRestricciones(): ?string
    {
        return $this->restricciones;
    }

    public function setRestricciones(?string $restricciones): self
    {
        $this->restricciones = $restricciones;
        return $this;
    }
}
