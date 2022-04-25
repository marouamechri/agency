<?php

namespace App\Entity;

use App\Repository\BienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BienRepository::class)]
class Bien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $titre;

    #[ORM\Column(type: 'integer')]
    private $nbPiece;

    #[ORM\Column(type: 'integer')]
    private $surface;

    #[ORM\Column(type: 'float')]
    private $prix;

    #[ORM\Column(type: 'string', length: 100)]
    private $localisation;

    #[ORM\Column(type: 'string', length: 50)]
    private $type;

    #[ORM\Column(type: 'integer')]
    private $etage;

    #[ORM\Column(type: 'string', length: 50)]
    private $transactionType;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'date')]
    private $dateConstruction;

    #[ORM\OneToMany(mappedBy: 'idbien', targetEntity: OptionBien::class)]
    private $optionBiens;

    public function __construct()
    {
        $this->optionBiens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNbPiece(): ?int
    {
        return $this->nbPiece;
    }

    public function setNbPiece(int $nbPiece): self
    {
        $this->nbPiece = $nbPiece;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEtage(): ?int
    {
        return $this->etage;
    }

    public function setEtage(int $etage): self
    {
        $this->etage = $etage;

        return $this;
    }

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateConstruction(): ?\DateTimeInterface
    {
        return $this->dateConstruction;
    }

    public function setDateConstruction(\DateTimeInterface $dateConstruction): self
    {
        $this->dateConstruction = $dateConstruction;

        return $this;
    }

    /**
     * @return Collection<int, OptionBien>
     */
    public function getOptionBiens(): Collection
    {
        return $this->optionBiens;
    }

    public function addOptionBien(OptionBien $optionBien): self
    {
        if (!$this->optionBiens->contains($optionBien)) {
            $this->optionBiens[] = $optionBien;
            $optionBien->setIdbien($this);
        }

        return $this;
    }

    public function removeOptionBien(OptionBien $optionBien): self
    {
        if ($this->optionBiens->removeElement($optionBien)) {
            // set the owning side to null (unless already changed)
            if ($optionBien->getIdbien() === $this) {
                $optionBien->setIdbien(null);
            }
        }

        return $this;
    }
}
