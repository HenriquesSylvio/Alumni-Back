<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\Event;
use App\Entity\Faculty;
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

    public function testgetFaculty_OkObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/faculty',
            '',
            [],
            false
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testdeleteFaculty_NotIdenticate(): void
    {

        $faculty = $this->entityManager
            ->getRepository(Faculty::class)
            ->findOneBy(['name' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/faculty/' . $faculty->getId(),
            '',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteFaculty_NotAdminConnect(): void
    {
        $faculty = $this->entityManager
            ->getRepository(Faculty::class)
            ->findOneBy(['name' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/faculty/'. $faculty->getId(),
            '',
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testdeleteFaculty_AdminConnect(): void
    {
        $faculty = $this->entityManager
            ->getRepository(Faculty::class)
            ->findOneBy(['name' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/faculty/' . $faculty->getId(),
            '',
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testupdateFaculty_NotIdenticate(): void
    {
        $faculty = $this->entityManager
            ->getRepository(Faculty::class)
            ->findOneBy(['name' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_PUT,
            '/api/faculty/' . $faculty->getId(),
            '{"name": "Ceci est un test"}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testupdateFaculty_NotAdminConnect(): void
    {
        $faculty = $this->entityManager
            ->getRepository(Faculty::class)
            ->findOneBy(['name' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_PUT,
            '/api/faculty/'. $faculty->getId(),
            '{"name": "Ceci est un test"}',
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testupdateFaculty_AdminConnect(): void
    {
        $faculty = $this->entityManager
            ->getRepository(Faculty::class)
            ->findOneBy(['name' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_PUT,
            '/api/faculty/' . $faculty->getId(),
            '{"name": "Ceci est un test"}',
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}