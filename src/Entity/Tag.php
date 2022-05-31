<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 * @UniqueEntity("label", message="Ce label existe déjà")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups("list", "getTag")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Le label du tag est obligatoire")
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups("list", "getTag")
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="tag", orphanRemoval=true)
     */
    private $posts;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setTag($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getTag() === $this) {
                $post->setTag(null);
            }
        }

        return $this;
    }
}
