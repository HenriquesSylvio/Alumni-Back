<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\Event;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class EventTest extends AbstractEndPoint
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

    public function testgetEventById_NotIdenticate(): void
    {
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['description' => 'Ceci est un test'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/event/' . $event->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetEventById_OkObjectResult(): void
    {
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['description' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/event/' . $event->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testdeleteEvent_NoContentResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['description' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/event/' . $event->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeleteEvent_AdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['description' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/event/' . $event->getId(),
            "",
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeleteEvent_NotAdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['description' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/event/' . $event->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteEvent_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['description' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/event/' . $event->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testaddEvent_CreatedResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/event',
            '{"title": "Ceci est un test", "description": "Ceci est un test", "date" : "' . date("d-m-Y") . '"}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testaddEvent_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/event',
            '{"title": "Ceci est un test", "description": "Ceci est un test", "date" : "' . date("d-m-Y") . '"}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetEvent_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/event',
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetEvent_OkObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/event',
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testaddParticipation_CreatedResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['author' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/event/participate',
            '{"event": {"id" : ' . $event->getId() . '}}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testaddParticipation_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['author' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/event/participate',
            '{"event": {"id" : ' . $event->getId() . '}}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testaddParticipation_EventAlreadyPast(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['author' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/event/participate',
            '{"event": {"id" : ' . $event->getId() . '}}',
            [],
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteParticipation_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['author' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/event/participation/' . $event->getId(),
            '',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteParticipation_NoContentResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['author' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/event/participation/' . $event->getId(),
            '',
            []
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeleteParticipation_EventAlreadyPast(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['author' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/event/participation/' . $event->getId(),
            '',
            []
        );
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
//
//
//    public function testupdateEvent_NoContentResult(): void
//    {
//        $user = $this->entityManager
//            ->getRepository(User::class)
//            ->findOneBy(['email' => 'user@outlook.fr'])
//        ;
//        $event = $this->entityManager
//            ->getRepository(Event::class)
//            ->findOneBy(['description' => 'Ceci est un test', 'author' => $user])
//        ;
//        $response = $this->getResponseFromRequest(
//            Request::METHOD_PUT,
//            '/api/event/' . $event->getId(),
//            '{"title": "Ceci est un test", "description": "Ceci est un test", "date" : "' . date("d-m-Y") . '"}',
//            [],
//            true,
//            false
//        );
//        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
//    }
//
//    public function testupdateEvent_WrongUserConnect(): void
//    {
//        $user = $this->entityManager
//            ->getRepository(User::class)
//            ->findOneBy(['email' => 'admin@outlook.fr'])
//        ;
//        $event = $this->entityManager
//            ->getRepository(Event::class)
//            ->findOneBy(['description' => 'Ceci est un test', 'author' => $user])
//        ;
//        $response = $this->getResponseFromRequest(
//            Request::METHOD_PUT,
//            '/api/event/' . $event->getId(),
//            '{"title": "Ceci est un test", "description": "Ceci est un test", "date" : "' . date("d-m-Y") . '"}',
//            [],
//            true,
//            false
//        );
//        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
//    }
//
//    public function testupdateEvent_NotIdenticate(): void
//    {
//        $user = $this->entityManager
//            ->getRepository(User::class)
//            ->findOneBy(['email' => 'admin@outlook.fr'])
//        ;
//        $event = $this->entityManager
//            ->getRepository(Event::class)
//            ->findOneBy(['description' => 'Ceci est un test', 'author' => $user])
//        ;
//        $response = $this->getResponseFromRequest(
//            Request::METHOD_PUT,
//            '/api/event/' . $event->getId(),
//            '{"title": "Ceci est un test", "description": "Ceci est un test", "date" : "' . date("d-m-Y") . '"}',
//            [],
//            false
//        );
//        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
//    }
}