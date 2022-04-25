<?php

namespace App\Entity;

use App\Repository\OptionBienRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionBienRepository::class)]
class OptionBien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Bien::class, inversedBy: 'optionBiens')]
    private $idbien;

    #[ORM\ManyToOne(targetEntity: Option::class, inversedBy: 'optionBiens')]
    private $idOption;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdbien(): ?Bien
    {
        return $this->idbien;
    }

    public function setIdbien(?Bien $idbien): self
    {
        $this->idbien = $idbien;

        return $this;
    }

    public function getIdOption(): ?Option
    {
        return $this->idOption;
    }

    public function setIdOption(?Option $idOption): self
    {
        $this->idOption = $idOption;

        return $this;
    }
}
