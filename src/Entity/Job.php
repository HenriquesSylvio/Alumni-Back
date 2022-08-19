<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups("list", "getJob")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre est obligatoire")
     * @Serializer\Groups("list", "getJob")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="La description est obligatoire")
     * @Serializer\Groups("list", "getJob")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La ville est obligatoire")
     * @Serializer\Groups("list", "getJob")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'entreprise est obligatoire")
     * @Serializer\Groups("list", "getJob")
     */
    private $company;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups("list", "getJob")
     */
    private $createAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La compensation est obligatoire")
     * @Serializer\Groups("list", "getJob")
     */
    private $compensation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="jobs")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups("list", "getJob")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Faculty::class, inversedBy="jobs")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups("list", "getJob")
     */
    private $faculty;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getCompensation(): ?string
    {
        return $this->compensation;
    }

    public function setCompensation(string $compensation): self
    {
        $this->compensation = $compensation;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getFaculty(): ?Faculty
    {
        return $this->faculty;
    }

    public function setFaculty(?Faculty $faculty): self
    {
        $this->faculty = $faculty;

        return $this;
    }
}
