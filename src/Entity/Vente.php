<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente extends Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?DetailsCommande $details = null;

    public function __construct()
    {
        $this->setType('VENTE');
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetails(): ?DetailsCommande
    {

        return $this->details;
    }

    public function setDetails(DetailsCommande $details): self
    {
        $this->details = $details;

        return $this;
    }
}
