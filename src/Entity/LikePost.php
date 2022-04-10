<?php

namespace App\Entity;

use App\Repository\LikePostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikePostRepository::class)
 */
class LikePost
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="likePosts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likePosts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->user;
    }

    public function setUsers(?User $users): self
    {
        $this->user = $users;

        return $this;
    }
}
