<?php

namespace App\Entity;

use App\Repository\SubscribeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=SubscribeRepository::class)
 */
class Subscribe
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscribes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscription;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscribes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscriber;

    public function getSubscription(): ?User
    {
        return $this->subscription;
    }

    public function setSubscription(?UserInterface $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getSubscriber(): ?User
    {
        return $this->subscriber;
    }

    public function setSubscriber(?UserInterface $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }
}
