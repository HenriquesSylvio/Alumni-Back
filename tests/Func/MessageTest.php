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

class MessageTest extends AbstractEndPoint
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
    public function testsendMessage_CreatedResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'henriques.sylvio@outlook.fr'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/message',
            '{ "content" : "test2", "received_by" : { "id": ' . $user->getId() . ' }}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testsendMessage_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'henriques.sylvio@outlook.fr'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/message',
            '{ "content" : "test2", "received_by" : { "id": ' . $user->getId() . ' }}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}