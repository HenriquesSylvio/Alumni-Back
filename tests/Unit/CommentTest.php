<?php

namespace App\Tests\Unit;

use App\Entity\Comment;
use App\Entity\LikePost;
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
}