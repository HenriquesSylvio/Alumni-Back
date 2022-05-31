<?php

namespace App\Entity;

use App\Repository\LikePostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=LikePostRepository::class)
 */
class LikePost
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="likePosts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $post;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likePosts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $likeBy;

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

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
