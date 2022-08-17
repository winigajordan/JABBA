<?php

namespace App\Entity;

use App\Repository\CodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodeRepository::class)]
class Code
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;



    #[ORM\Column]
    private ?float $reduction = null;

    #[ORM\OneToMany(mappedBy: 'code', targetEntity: CommandeReduction::class)]
    private Collection $commandeReductions;

    public function __construct()
    {
        $this->commandeReductions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }


    public function getReduction(): ?float
    {
        return $this->reduction;
    }

    public function setReduction(float $reduction): self
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * @return Collection<int, CommandeReduction>
     */
    public function getCommandeReductions(): Collection
    {
        return $this->commandeReductions;
    }

    public function addCommandeReduction(CommandeReduction $commandeReduction): self
    {
        if (!$this->commandeReductions->contains($commandeReduction)) {
            $this->commandeReductions[] = $commandeReduction;
            $commandeReduction->setCode($this);
        }

        return $this;
    }

    public function removeCommandeReduction(CommandeReduction $commandeReduction): self
    {
        if ($this->commandeReductions->removeElement($commandeReduction)) {
            // set the owning side to null (unless already changed)
            if ($commandeReduction->getCode() === $this) {
                $commandeReduction->setCode(null);
            }
        }

        return $this;
    }
}
