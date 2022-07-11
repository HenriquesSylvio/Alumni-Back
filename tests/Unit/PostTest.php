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