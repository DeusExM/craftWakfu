<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
class Inventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(mappedBy: 'inventory', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $user;

    #[ORM\OneToMany(mappedBy: 'inventory', targetEntity: InventoryItems::class, orphanRemoval: true)]
    private $inventoryItems;

    public function __toString(): string
    {
        return $this->getId();
    }

    public function __construct()
    {
        $this->inventoryItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setInventory(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getInventory() !== $this) {
            $user->setInventory($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, InventoryItems>
     */
    public function getInventoryItems(): Collection
    {
        return $this->inventoryItems;
    }

    public function addInventoryItem(InventoryItems $inventoryItem): self
    {
        if (!$this->inventoryItems->contains($inventoryItem)) {
            $this->inventoryItems[] = $inventoryItem;
            $inventoryItem->setInventory($this);
        }

        return $this;
    }

    public function removeInventoryItem(InventoryItems $inventoryItem): self
    {
        if ($this->inventoryItems->removeElement($inventoryItem)) {
            // set the owning side to null (unless already changed)
            if ($inventoryItem->getInventory() === $this) {
                $inventoryItem->setInventory(null);
            }
        }

        return $this;
    }
}
