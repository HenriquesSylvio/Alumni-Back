<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class UserTest extends AbstractEndPoint
{
    private string $userPayload = '{"id : 2 ,email": "%s", "password": "S-t5S-t5", "first_name": "test", "last_name": "test", "birthday": "25-09-1999", "promo": "25-09-2021"}';
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

    public function testgetUserById_OkObjectResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'henriques.sylvio@outlook.fr'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/user/' . $user->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testgetUserById_NotFoundObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/user/5000',
            "",
            []
        );
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testgetUserById_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/user/5000',
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testacceptUser_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_PATCH,
            '/api/user/' . $user->getId() + 1,
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testacceptUser_NotAdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_PATCH,
            '/api/user/' . $user->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testacceptUser_AdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_PATCH,
            '/api/user/' . $user->getId(),
            "",
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testgetMyProfile_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/user/me',
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetMyProfile_Identicate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/user/me',
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    private function getPayload(): string
    {
        $faker = Factory::create();

        return sprintf($this->userPayload, $faker->email);
    }

}