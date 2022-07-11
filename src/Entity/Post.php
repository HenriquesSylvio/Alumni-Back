<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups("list", "getPost")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le contenu est obligatoire")
     * @Serializer\Groups("list", "getPost")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups("list", "getPost")
     */
    private $createAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups("list", "getPost")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=LikePost::class, mappedBy="post", orphanRemoval=true)
     */
    private $likePosts;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class)
     */
    private $mainPost;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class)
     */
    private $parentPost;

    public function __construct()
    {
        $this->likePosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, LikePost>
     */
    public function getLikePosts(): Collection
    {
        return $this->likePosts;
    }

    public function addLikePost(LikePost $likePost): self
    {
        if (!$this->likePosts->contains($likePost)) {
            $this->likePosts[] = $likePost;
            $likePost->setPost($this);
        }

        return $this;
    }

    public function removeLikePost(LikePost $likePost): self
    {
        if ($this->likePosts->removeElement($likePost)) {
            // set the owning side to null (unless already changed)
            if ($likePost->getPost() === $this) {
                $likePost->setPost(null);
            }
        }

        return $this;
    }

    public function getMainPost(): ?self
    {
        return $this->mainPost;
    }

    public function setMainPost(?self $mainPost): self
    {
        $this->mainPost = $mainPost;

        return $this;
    }

    public function getParentPost(): ?self
    {
        return $this->parentPost;
    }

    public function setParentPost(?self $parentPost): self
    {
        $this->parentPost = $parentPost;

        return $this;
    }
}
