<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le contenu est obligatoire")
     * @Serializer\Groups("list", "getComment")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups("list", "getComment")
     */
    private $createAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="L'id du post est obligatoire'")
     */
    private $post;

    /**
     * @ORM\OneToMany(targetEntity=ReplyComment::class, mappedBy="answerComment")
     */
    private $replyComments;

    public function __construct()
    {
        $this->replyComments = new ArrayCollection();
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Collection<int, ReplyComment>
     */
    public function getReplyComments(): Collection
    {
        return $this->replyComments;
    }

    public function addReplyComment(ReplyComment $replyComment): self
    {
        if (!$this->replyComments->contains($replyComment)) {
            $this->replyComments[] = $replyComment;
            $replyComment->setAnswerComment($this);
        }

        return $this;
    }

    public function removeReplyComment(ReplyComment $replyComment): self
    {
        if ($this->replyComments->removeElement($replyComment)) {
            // set the owning side to null (unless already changed)
            if ($replyComment->getAnswerComment() === $this) {
                $replyComment->setAnswerComment(null);
            }
        }

        return $this;
    }
}
