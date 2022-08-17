<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Adresse::class, orphanRemoval: true)]
    private Collection $adresse;

    #[ORM\OneToOne(mappedBy: 'compte', cascade: ['persist', 'remove'])]
    private ?Wallet $wallet = null;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?Boutique $boutique = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Commande::class, orphanRemoval: true)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Favoris::class)]
    private Collection $favoris;

    public function __construct(){
        $this->setRoles(["ROLE_CLIENT"]);
        $this->adresse = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * @return Collection<int, Adresse>
     */
    public function getAdresse(): Collection
    {
        return $this->adresse;
    }

    public function addAdresse(Adresse $adresse): self
    {
        if (!$this->adresse->contains($adresse)) {
            $this->adresse[] = $adresse;
            $adresse->setClient($this);
        }

        return $this;
    }

    public function removeAdresse(Adresse $adresse): self
    {
        if ($this->adresse->removeElement($adresse)) {
            // set the owning side to null (unless already changed)
            if ($adresse->getClient() === $this) {
                $adresse->setClient(null);
            }
        }

        return $this;
    }

    public function getDefaultAdresse(): ?Adresse{
        $adds = $this->getAdresse();
        return $adds[0];
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        // unset the owning side of the relation if necessary
        if ($wallet === null && $this->wallet !== null) {
            $this->wallet->setCompte(null);
        }

        // set the owning side of the relation if necessary
        if ($wallet !== null && $wallet->getCompte() !== $this) {
            $wallet->setCompte($this);
        }

        $this->wallet = $wallet;

        return $this;
    }

    public function getBoutique(): ?Boutique
    {
        return $this->boutique;
    }

    public function setBoutique(?Boutique $boutique): self
    {
        // unset the owning side of the relation if necessary
        if ($boutique === null && $this->boutique !== null) {
            $this->boutique->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($boutique !== null && $boutique->getClient() !== $this) {
            $boutique->setClient($this);
        }

        $this->boutique = $boutique;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setClient($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getClient() === $this) {
                $commande->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favoris>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Favoris $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
            $favori->setClient($this);
        }

        return $this;
    }

    public function removeFavori(Favoris $favori): self
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getClient() === $this) {
                $favori->setClient(null);
            }
        }

        return $this;
    }

}
