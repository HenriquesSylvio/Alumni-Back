<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Entity\Faculty;

class FacultyTest extends TestCase
{
    private Faculty $faculty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faculty = new Faculty();
    }

    public function testGetName(): void
    {
        $value = 'test';

        $response = $this->faculty->setName($value);

        self::assertInstanceOf(Faculty::class, $response);
        self::assertEquals($value, $this->faculty->getName());
    }

    public function testGetUser(): void
    {
        $value = new User();

        $response = $this->faculty->addUser($value);

        self::assertInstanceOf(Faculty::class, $response);
        self::assertCount(1, $this->faculty->getUsers());
        self::assertTrue($this->faculty->getUsers()->contains($value));

        $response = $this->faculty->removeUser($value);

        self::assertInstanceOf(Faculty::class, $response);
        self::assertCount(0, $this->faculty->getUsers());
        self::assertFalse($this->faculty->getUsers()->contains($value));
    }

    public function testGetUsers(): void
    {
        $value = new User();
        $value1 = new User();
        $value2 = new User();

        $this->faculty->addUser($value);
        $this->faculty->addUser($value1);
        $this->faculty->addUser($value2);

        self::assertCount(3, $this->faculty->getUsers());
        self::assertTrue($this->faculty->getUsers()->contains($value));
        self::assertTrue($this->faculty->getUsers()->contains($value1));
        self::assertTrue($this->faculty->getUsers()->contains($value2));

        $response = $this->faculty->removeUser($value);

        self::assertInstanceOf(Faculty::class, $response);
        self::assertCount(2, $this->faculty->getUsers());
        self::assertFalse($this->faculty->getUsers()->contains($value));
        self::assertTrue($this->faculty->getUsers()->contains($value1));
        self::assertTrue($this->faculty->getUsers()->contains($value2));
    }
}