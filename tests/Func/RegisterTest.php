<?php

namespace App\Tests\Func;

use App\Entity\Faculty;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterTest extends AbstractEndPoint
{
    private string $userPayload = '{"email": "%s", "password": "S-t5S-t5", "username": "%s", "first_name": "test", "last_name": "test", "promo": "2017", "faculty": {"id" : "%s"}}';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testcreateUser(): void
    {
    $response = $this->getResponseFromRequest(
        Request::METHOD_POST,
        '/api/register',
        $this->getPayload(),
        [],
        false
    );

    self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    private function getPayload(): string
    {
        $faker = Factory::create();
//        $faculty = $this->doctrine->getRepository(Faculty::class)->findBy();
        $faculty = $this->entityManager
            ->getRepository(Faculty::class)
            ->findOneBy(['Name' => 'Autre'])
        ;

        return sprintf($this->userPayload, $faker->email, $faker->userName, $faculty->getId());
    }

}