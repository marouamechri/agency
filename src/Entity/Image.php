<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $photo;

    #[ORM\ManyToOne(targetEntity: Bien::class, inversedBy: 'images')]
    private $idBien;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getIdBien(): ?Bien
    {
        return $this->idBien;
    }

    public function setIdBien(?Bien $idBien): self
    {
        $this->idBien = $idBien;

        return $this;
    }
}
