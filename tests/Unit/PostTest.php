<?php

namespace App\Tests\Unit;

use App\Entity\Comment;
use App\Entity\LikePost;
use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->post = new Post();
    }

    public function testGetContent(): void
    {
        $value = 'Ceci est un test';

        $response = $this->post->setContent($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertEquals($value, $this->post->getContent());
    }

    public function testGetCreateAt(): void
    {
        $value = new \DateTime(date("d-m-Y"));

        $response = $this->post->setCreateAt($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertEquals($value, $this->post->getCreateAt());
    }

    public function testGetComment(): void
    {
        $value = new Comment();

        $response = $this->post->addComment($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertCount(1, $this->post->getComments());
        self::assertTrue($this->post->getComments()->contains($value));

        $response = $this->post->removeComment($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertCount(0, $this->post->getComments());
        self::assertFalse($this->post->getComments()->contains($value));
    }

    public function testGetComments(): void
    {
        $value = new Comment();
        $value1 = new Comment();
        $value2 = new Comment();

        $this->post->addComment($value);
        $this->post->addComment($value1);
        $this->post->addComment($value2);

        self::assertCount(3, $this->post->getComments());
        self::assertTrue($this->post->getComments()->contains($value));
        self::assertTrue($this->post->getComments()->contains($value1));
        self::assertTrue($this->post->getComments()->contains($value2));

        $response = $this->post->removeComment($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertCount(2, $this->post->getComments());
        self::assertFalse($this->post->getComments()->contains($value));
        self::assertTrue($this->post->getComments()->contains($value1));
        self::assertTrue($this->post->getComments()->contains($value2));
    }

    public function testGetLikePost(): void
    {
        $value = new LikePost();

        $response = $this->post->addLikePost($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertCount(1, $this->post->getLikePosts());
        self::assertTrue($this->post->getLikePosts()->contains($value));

        $response = $this->post->removeLikePost($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertCount(0, $this->post->getLikePosts());
        self::assertFalse($this->post->getLikePosts()->contains($value));
    }

    public function testGetLikePosts(): void
    {
        $value = new LikePost();
        $value1 = new LikePost();
        $value2 = new LikePost();

        $this->post->addLikePost($value);
        $this->post->addLikePost($value1);
        $this->post->addLikePost($value2);

        self::assertCount(3, $this->post->getLikePosts());
        self::assertTrue($this->post->getLikePosts()->contains($value));
        self::assertTrue($this->post->getLikePosts()->contains($value1));
        self::assertTrue($this->post->getLikePosts()->contains($value2));

        $response = $this->post->removeLikePost($value);

        self::assertInstanceOf(Post::class, $response);
        self::assertCount(2, $this->post->getLikePosts());
        self::assertFalse($this->post->getLikePosts()->contains($value));
        self::assertTrue($this->post->getLikePosts()->contains($value1));
        self::assertTrue($this->post->getLikePosts()->contains($value2));
    }
}