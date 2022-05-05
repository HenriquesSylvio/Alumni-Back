<?php

namespace App\Tests\Unit;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\LikePost;
use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = new Event();
    }

    public function testGetTitle(): void
    {
        $value = 'Ceci est un test';

        $response = $this->event->setTitle($value);

        self::assertInstanceOf(Event::class, $response);
        self::assertEquals($value, $this->event->getTitle());
    }

    public function testGetDescription(): void
    {
        $value = 'Ceci est un test';

        $response = $this->event->setDescription($value);

        self::assertInstanceOf(Event::class, $response);
        self::assertEquals($value, $this->event->getDescription());
    }

    public function testGetDatet(): void
    {
        $value = new \DateTime(date("d-m-Y"));

        $response = $this->event->setDate($value);

        self::assertInstanceOf(Event::class, $response);
        self::assertEquals($value, $this->event->getDate());
    }
}