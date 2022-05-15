<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\LikePost;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class TagTest extends AbstractEndPoint
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
    public function testaddTag_CreatedResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/tag',
            '{"label": "Test2"}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testaddTag_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/tag',
            '{"label": "Test2"}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testaddTag_NotAdminConnect(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/tag',
            '{"label": "Test2"}',
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testdeleteTag_NotIdenticate(): void
    {
        $tag = $this->entityManager
            ->getRepository(Tag::class)
            ->findOneBy(['label' => 'Test2'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/tag/' . $tag->getId(),
            '',
            [],
            false
        );

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteTag_NotAdminConnect(): void
    {
        $tag = $this->entityManager
            ->getRepository(Tag::class)
            ->findOneBy(['label' => 'Test2'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/tag/' .  $tag->getId(),
            '',
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testdeleteTag_NoContentResult(): void
    {
        $tag = $this->entityManager
            ->getRepository(Tag::class)
            ->findOneBy(['label' => 'Test2'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/tag/' . $tag->getId(),
            '',
            []
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testgetTag_OkObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/tag',
            '',
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testgetTag_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/tag',
            '',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}