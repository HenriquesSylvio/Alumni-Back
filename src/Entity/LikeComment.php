<?php

namespace App\Entity;

use App\Repository\LikeCommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=LikeCommentRepository::class)
 */
class LikeComment
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="likeComments")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $comment;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likeComments")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $likeBy;

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getUsers(): ?UserInterface
    {
        return $this->likeBy;
    }

    public function setUsers(?UserInterface $users): self
    {
        $this->likeBy = $users;

        return $this;
    }
}
