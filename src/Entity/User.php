<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Serializable as SerializableAlias;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, SerializableAlias, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $familyName;

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: Inventory::class, cascade: ['persist', 'remove'])]
    private ?Inventory $inventory;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Sale::class)]
    private $sales;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ItemToCraft::class)]
    private $itemToCrafts;

    public function __construct()
    {
        $this->sales = new ArrayCollection();
        $this->itemToCrafts = new ArrayCollection();
    }

    /**
     * @return string
     */
    #[Pure] public function __toString() :string
    {
        return $this->getFirstName() . ' ' . $this->getFamilyName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function serialize(): ?string
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize(string $data)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($data, array('allowed_classes' => false));
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Sale>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
            $sale->setUser($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getUser() === $this) {
                $sale->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemToCraft>
     */
    public function getItemToCrafts(): Collection
    {
        return $this->itemToCrafts;
    }

    public function addItemToCraft(ItemToCraft $itemToCraft): self
    {
        if (!$this->itemToCrafts->contains($itemToCraft)) {
            $this->itemToCrafts[] = $itemToCraft;
            $itemToCraft->setUser($this);
        }

        return $this;
    }

    public function removeItemToCraft(ItemToCraft $itemToCraft): self
    {
        if ($this->itemToCrafts->removeElement($itemToCraft)) {
            // set the owning side to null (unless already changed)
            if ($itemToCraft->getUser() === $this) {
                $itemToCraft->setUser(null);
            }
        }

        return $this;
    }
}
