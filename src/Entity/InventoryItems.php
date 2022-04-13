<?php

namespace App\Entity;

use App\Repository\InventoryItemsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryItemsRepository::class)]
#[ORM\UniqueConstraint(name: "unique_asset", columns: ["item_id", "inventory_id"])]
class InventoryItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'inventoryItems')]
    #[ORM\JoinColumn(nullable: false)]
    private $item;

    #[ORM\ManyToOne(targetEntity: Inventory::class, inversedBy: 'inventoryItems')]
    #[ORM\JoinColumn(nullable: false)]
    private $inventory;

    #[ORM\Column(type: 'integer')]
    private $quantity;

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

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
