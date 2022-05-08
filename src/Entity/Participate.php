<?php

namespace App\Entity;

use App\Repository\ParticipateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ParticipateRepository::class)
 */
class Participate
{
    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="participates")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="participates")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     * @Serializer\Groups("list", "getParticipation")
     */
    private $participant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?UserInterface $participant): self
    {
        $this->participant = $participant;

        return $this;
    }
}
