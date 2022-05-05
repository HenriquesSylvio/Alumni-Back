<?php

namespace App\Entity;

use App\Repository\ReplyCommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReplyCommentRepository::class)
 */
class ReplyComment
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="replyComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $answerComment;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="replyComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $replyComment;

    public function getAnswerComment(): ?Comment
    {
        return $this->answerComment;
    }

    public function setAnswerComment(?Comment $answerComment): self
    {
        $this->answerComment = $answerComment;

        return $this;
    }

    public function getReplyComment(): ?Comment
    {
        return $this->replyComment;
    }

    public function setReplyComment(?Comment $replyComment): self
    {
        $this->replyComment = $replyComment;

        return $this;
    }
}
