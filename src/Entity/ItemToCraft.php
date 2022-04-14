<?php

namespace App\Entity;

use App\Repository\ItemToCraftRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemToCraftRepository::class)]
#[ORM\UniqueConstraint(name: "unique_product", columns: ["item_id", "user_id"])]
class ItemToCraft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'itemToCrafts')]
    #[ORM\JoinColumn(nullable: false)]
    private $item;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'itemToCrafts')]
    private $user;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
