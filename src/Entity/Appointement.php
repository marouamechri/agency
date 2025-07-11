<?php

namespace App\Entity;

use App\Entity\Bien;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AppointementRepository;

#[ORM\Entity(repositoryClass: AppointementRepository::class)]
class Appointement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $date;

       #[ORM\Column(type: 'string', length: 100)]
    private $email;

    #[ORM\Column(type: 'string', length: 14)]
    private $tel;

    #[ORM\Column(type: 'string', length: 20)]
    private $nom;

    #[ORM\Column(type: 'string', length: 20)]
    private $prenom;

    #[ORM\ManyToOne(targetEntity: Bien::class, inversedBy: 'appointements')]
    #[ORM\JoinColumn(nullable: false)]
    private $titre;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

       
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTitre(): ?Bien
    {
        return $this->titre;
    }

    public function setTitre(?Bien $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

   
}
