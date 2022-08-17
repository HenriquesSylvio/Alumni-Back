<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\Faculty;
use App\Entity\Job;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class JobTest extends AbstractEndPoint
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
    public function testgetJob_OkObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/job',
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testgetJob_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/job',
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetJobsByUser_OkObjectResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'henriques.sylvio@outlook.fr'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/job/user/' . $user->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testgetJobsByUser_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'henriques.sylvio@outlook.fr'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/jobs/user/' . $user->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetJobById_NotIdenticate(): void
    {
        $job = $this->entityManager
            ->getRepository(Job::class)
            ->findOneBy(['title' => 'Titre test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/job/' . $job->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetJobById_OkObjectResult(): void
    {
        $job = $this->entityManager
            ->getRepository(Job::class)
            ->findOneBy(['title' => 'Titre test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/job/' . $job->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testaddJob_CreatedResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/job',
            '{"title": "Titre test", "description": "Ceci est un test", "city": "Rouen", "company": "Normandie Web School", "compensation": "2000€ net par mois", "faculty_id": "1"}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testaddJob_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/job',
            '{"title": "Titre test", "description": "Ceci est un test", "city": "Rouen", "company": "Normandie Web School", "compensation": "2000€ net par mois", "faculty_id": "1"}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }


    public function testgetFeedJob_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/job/feed',
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetFeedJob_OkObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/job/feed',
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testdeleteJob_NoContentResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $job = $this->entityManager
            ->getRepository(Job::class)
            ->findOneBy(['title' => 'Titre test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/job/' . $job->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeleteJob_AdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $job = $this->entityManager
            ->getRepository(Job::class)
            ->findOneBy(['title' => 'Titre test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/job/' . $job->getId(),
            "",
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeleteJob_NotAdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $job = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['title' => 'Titre test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/job/' . $job->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteJob_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $job = $this->entityManager
            ->getRepository(Job::class)
            ->findOneBy(['title' => 'Titre test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/job/' . $job->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}