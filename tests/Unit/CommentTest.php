<?php

namespace App\Tests\Unit;

use App\Entity\Comment;
use App\Entity\LikeComment;
use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    private Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comment = new Comment();
    }

    public function testGetContent(): void
    {
        $value = 'Ceci est un test';

        $response = $this->comment->setContent($value);

        self::assertInstanceOf(Comment::class, $response);
        self::assertEquals($value, $this->comment->getContent());
    }

    public function testGetCreateAt(): void
    {
        $value = new \DateTime(date("d-m-Y"));

        $response = $this->comment->setCreateAt($value);

        self::assertInstanceOf(Comment::class, $response);
        self::assertEquals($value, $this->comment->getCreateAt());
    }

    public function testGetPost(): void
    {
        $value = new Post();

        $response = $this->comment->setPost($value);

        self::assertInstanceOf(Comment::class, $response);
        self::assertEquals($value, $this->comment->getPost());
    }

    public function testGetLikeComment(): void
    {
        $value = new LikeComment();

        $response = $this->comment->addLikeComment($value);

        self::assertInstanceOf(Comment::class, $response);
        self::assertCount(1, $this->comment->getLikeComments());
        self::assertTrue($this->comment->getLikeComments()->contains($value));

        $response = $this->comment->removeLikeComment($value);

        self::assertInstanceOf(Comment::class, $response);
        self::assertCount(0, $this->comment->getLikeComments());
        self::assertFalse($this->comment->getLikeComments()->contains($value));
    }

    public function testGetLikeComments(): void
    {
        $value = new LikeComment();
        $value1 = new LikeComment();
        $value2 = new LikeComment();

        $this->comment->addLikeComment($value);
        $this->comment->addLikeComment($value1);
        $this->comment->addLikeComment($value2);

        self::assertCount(3, $this->comment->getLikeComments());
        self::assertTrue($this->comment->getLikeComments()->contains($value));
        self::assertTrue($this->comment->getLikeComments()->contains($value1));
        self::assertTrue($this->comment->getLikeComments()->contains($value2));

        $response = $this->comment->removeLikeComment($value);

        self::assertInstanceOf(Comment::class, $response);
        self::assertCount(2, $this->comment->getLikeComments());
        self::assertFalse($this->comment->getLikeComments()->contains($value));
        self::assertTrue($this->comment->getLikeComments()->contains($value1));
        self::assertTrue($this->comment->getLikeComments()->contains($value2));
    }
}