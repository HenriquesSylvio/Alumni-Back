<?php

namespace App\Tests\Unit;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
    }

    public function testGetEmail(): void
    {
        $value = 'test@test.fr';

        $response = $this->user->setEmail($value);

        self::assertInstanceOf(User::class, $response);
        self::assertEquals($value, $this->user->getEmail());
        self::assertEquals($value, $this->user->getUsername());
    }

    public function testGetRoles(): void
    {
        $value = ['ROLE_ADMIN'];

        $response = $this->user->setRoles($value);

        self::assertInstanceOf(User::class, $response);
        self::assertContains('ROLE_USER', $this->user->getRoles());
        self::assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testGetPassword(): void
    {
        $value = 'password';

        $response = $this->user->setPassword($value);

        self::assertInstanceOf(User::class, $response);
        self::assertEquals($value, $this->user->getPassword());
    }

    public function testGetFirstName(): void
    {
        $value = 'FirstName';

        $response = $this->user->setFirstName($value);

        self::assertInstanceOf(User::class, $response);
        self::assertEquals($value, $this->user->getFirstName());
    }

    public function testGetLasttName(): void
    {
        $value = 'LastName';

        $response = $this->user->setLastName($value);

        self::assertInstanceOf(User::class, $response);
        self::assertEquals($value, $this->user->getLastName());
    }

    public function testGetPromo(): void
    {
        $value = 2017;

        $response = $this->user->setPromo($value);

        self::assertInstanceOf(User::class, $response);
        self::assertEquals($value, $this->user->getPromo());
    }

    public function testGetPost(): void
    {
        $value = new Post();

        $response = $this->user->addPost($value);

        self::assertInstanceOf(User::class, $response);
        self::assertCount(1, $this->user->getPosts());
        self::assertTrue($this->user->getPosts()->contains($value));

        $response = $this->user->removePost($value);

        self::assertInstanceOf(User::class, $response);
        self::assertCount(0, $this->user->getPosts());
        self::assertFalse($this->user->getPosts()->contains($value));
    }

    public function testGetPosts(): void
    {
        $value = new Post();
        $value1 = new Post();
        $value2 = new Post();

        $this->user->addPost($value);
        $this->user->addPost($value1);
        $this->user->addPost($value2);

        self::assertCount(3, $this->user->getPosts());
        self::assertTrue($this->user->getPosts()->contains($value));
        self::assertTrue($this->user->getPosts()->contains($value1));
        self::assertTrue($this->user->getPosts()->contains($value2));

        $response = $this->user->removePost($value);

        self::assertInstanceOf(User::class, $response);
        self::assertCount(2, $this->user->getPosts());
        self::assertFalse($this->user->getPosts()->contains($value));
        self::assertTrue($this->user->getPosts()->contains($value1));
        self::assertTrue($this->user->getPosts()->contains($value2));
    }
}