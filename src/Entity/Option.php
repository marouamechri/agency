<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`option`')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $designation;

    #[ORM\OneToMany(mappedBy: 'idOption', targetEntity: OptionBien::class, cascade:['persist'])]
    private $optionBiens;

    public function __construct()
    {
        $this->optionBiens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

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
            $optionBien->setIdOption($this);
        }

        return $this;
    }

    public function removeOptionBien(OptionBien $optionBien): self
    {
        if ($this->optionBiens->removeElement($optionBien)) {
            // set the owning side to null (unless already changed)
            if ($optionBien->getIdOption() === $this) {
                $optionBien->setIdOption(null);
            }
        }

        return $this;
    }
}
