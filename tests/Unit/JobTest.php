<?php

namespace App\Tests\Unit;

use App\Entity\Job;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    private Job $job;

    protected function setUp(): void
    {
        parent::setUp();

        $this->job = new Job();
    }

    public function testGetTitle(): void
    {
        $value = 'Titre test';

        $response = $this->job->setTitle($value);

        self::assertInstanceOf(Job::class, $response);
        self::assertEquals($value, $this->job->getTitle());
    }

    public function testGetDescription(): void
    {
        $value = 'Ceci est un test';

        $response = $this->job->setDescription($value);

        self::assertInstanceOf(Job::class, $response);
        self::assertEquals($value, $this->job->getDescription());
    }

    public function testGetCity(): void
    {
        $value = 'Rouen';

        $response = $this->job->setCity($value);

        self::assertInstanceOf(Job::class, $response);
        self::assertEquals($value, $this->job->getCity());
    }

    public function testGetCompany(): void
    {
        $value = 'Normandie Web School';

        $response = $this->job->setCompany($value);

        self::assertInstanceOf(Job::class, $response);
        self::assertEquals($value, $this->job->getCompany());
    }

    public function testGetCreateAt(): void
    {
        $value = new \DateTime(date("d-m-Y"));

        $response = $this->job->setCreateAt($value);

        self::assertInstanceOf(Job::class, $response);
        self::assertEquals($value, $this->job->getCreateAt());
    }

    public function testGetCompensation(): void
    {
        $value = '2000€ net par mois';

        $response = $this->job->setCompensation($value);

        self::assertInstanceOf(Job::class, $response);
        self::assertEquals($value, $this->job->getCompensation());
    }

}