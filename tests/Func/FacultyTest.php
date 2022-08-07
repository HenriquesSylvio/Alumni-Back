<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\Event;
use App\Entity\Participate;
use App\Entity\Post;
use App\Entity\User;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class FacultyTest extends AbstractEndPoint
{
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

    public function testaddFaculty_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/faculty',
            '{"name": "Ceci est un test"}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testaddFaculty_NotAdminConnect(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/faculty',
            '{"name": "Ceci est un test"}',
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testaddFaculty_AdminConnect(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/faculty',
            '{"name": "Ceci est un test"}',
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

}