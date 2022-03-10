<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private $job;

    #[ORM\Column(type: 'integer')]
    private $reference;

    #[ORM\Column(type: 'string', length: 255)]
    private $rarity;

    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $lvlItem;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $lvlCraft;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $upgrade;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nb;

    #[ORM\OneToOne(inversedBy: 'item', targetEntity: Recipe::class, cascade: ['persist', 'remove'])]
    private $recipe;

    #[ORM\Column(type: 'float')]
    private $averagePrice = 0;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: InventoryItems::class, orphanRemoval: true)]
    private $inventoryItems;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Ingredient::class)]
    private $ingredients;

    public function __toString(): string
    {
        return $this->getName() . ' ' . $this->getLvlItem() . ' [' . $this->getRarity() . ']';
    }

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->inventoryItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getReference(): ?int
    {
        return $this->reference;
    }

    public function setReference(int $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getRarity(): ?string
    {
        return $this->rarity;
    }

    public function setRarity(string $rarity): self
    {
        $this->rarity = $rarity;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLvlItem(): ?int
    {
        return $this->lvlItem;
    }

    public function setLvlItem(?int $lvlItem): self
    {
        $this->lvlItem = $lvlItem;

        return $this;
    }

    public function getLvlCraft(): ?int
    {
        return $this->lvlCraft;
    }

    public function setLvlCraft(?int $lvlCraft): self
    {
        $this->lvlCraft = $lvlCraft;

        return $this;
    }

    public function getUpgrade(): ?bool
    {
        return $this->upgrade;
    }

    public function setUpgrade(?bool $upgrade): self
    {
        $this->upgrade = $upgrade;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNb(): ?int
    {
        return $this->nb;
    }

    public function setNb(?int $nb): self
    {
        $this->nb = $nb;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setItem($ingredient->getItem());
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getItem() === $this) {
                $ingredient->setItem(null);
            }
        }

        return $this;
    }

    public function getAveragePrice(): ?float
    {
        return $this->averagePrice;
    }

    public function setAveragePrice(float $averagePrice): self
    {
        $this->averagePrice = $averagePrice;

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
            $inventoryItem->setItem($this);
        }

        return $this;
    }

    public function removeInventoryItem(InventoryItems $inventoryItem): self
    {
        if ($this->inventoryItems->removeElement($inventoryItem)) {
            // set the owning side to null (unless already changed)
            if ($inventoryItem->getItem() === $this) {
                $inventoryItem->setItem(null);
            }
        }

        return $this;
    }
}
