<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'sales')]
    #[ORM\JoinColumn(nullable: false)]
    private $item;

    #[ORM\Column(type: 'float')]
    private $tax;

    #[ORM\Column(type: 'float')]
    private $sale;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $forSaleAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $saleAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sales')]
    private $user;

    #[ORM\Column(type: 'float', nullable: true)]
    private $extraCost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(float $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getSale(): ?float
    {
        return $this->sale;
    }

    public function setSale(float $sale): self
    {
        $this->sale = $sale;

        return $this;
    }

    public function getSaleAt(): ?\DateTimeImmutable
    {
        return $this->saleAt;
    }

    public function setSaleAt(?\DateTimeImmutable $saleAt): self
    {
        $this->saleAt = $saleAt;

        return $this;
    }

    public function getForSaleAt(): ?\DateTimeImmutable
    {
        return $this->forSaleAt;
    }

    public function setForSaleAt(?\DateTimeImmutable $forSaleAt): self
    {
        $this->forSaleAt = $forSaleAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExtraCost(): ?float
    {
        return $this->extraCost;
    }

    public function setExtraCost(?float $extraCost): self
    {
        $this->extraCost = $extraCost;

        return $this;
    }
}
