<?php

namespace App\Tests\Func;


use App\Entity\Faculty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $this->faculty = new Faculty();
        $this->faculty->setName('Test');
    }

    public function testaddFaculty_AdminConnect(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/faculty/create',
            '{"name": "TestDev"}',
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testaddFaculty_NotAdminConnect(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/faculty/create',
            '{"name": "TestDev"}',
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}