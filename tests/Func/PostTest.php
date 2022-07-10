<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\LikePost;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class PostTest extends AbstractEndPoint
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

        $this->post = New Post();
        $this->post->setContent("Ceci est un test");
    }
    public function testgetPost_OkObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post',
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testgetPost_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post',
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetPostsByUser_OkObjectResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'henriques.sylvio@outlook.fr'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post/user/' . $user->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testgetPostsByUser_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'henriques.sylvio@outlook.fr'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post/user/' . $user->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetPosTById_NotIdenticate(): void
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post/' . $post->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetPosTById_OkObjectResult(): void
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post/' . $post->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testdeletePost_NoContentResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/post/' . $post->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeletePost_AdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/post/' . $post->getId(),
            "",
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeletePost_NotAdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/post/' . $post->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeletePost_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/post/' . $post->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testaddPost_CreatedResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/post',
            '{"title": "Ceci est un test","content": "Ceci est un test"}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testaddPost_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/post',
            '{"title": "Ceci est un test","content": "Ceci est un test"}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testaddLikePost_CreatedResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/post/like',
            '{"post": {"id" : ' . $post->getId() . '}}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

//        $this->getResponseFromRequest(
//            Request::METHOD_DELETE,
//            '/api/post/like/' . $post->getId(),
//            '',
//            [],
//            false
//        );
    }

    public function testaddLikePost_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/post/like',
            '{"post": {"id" : ' . $post->getId() . '}}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

//    public function testdeleteLikePost_NotTherightUser(): void
//    {
//        $user = $this->entityManager
//            ->getRepository(User::class)
//            ->findOneBy(['email' => 'admin@outlook.fr'])
//        ;
//        $post = $this->entityManager
//            ->getRepository(Post::class)
//            ->findOneBy(['author' => $user])
//        ;
//        $response = $this->getResponseFromRequest(
//            Request::METHOD_DELETE,
//            '/api/post/like/' . $post->getId(),
//            '',
//            [],
//            true,
//            false
//        );
//        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
//    }

    public function testdeleteLikePost_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $likePost = $this->entityManager
            ->getRepository(LikePost::class)
            ->findOneBy(['likeBy' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/post/like/' . $likePost->getPost()->getId(),
            '',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteLikePost_NoContentResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $likePost = $this->entityManager
            ->getRepository(LikePost::class)
            ->findOneBy(['likeBy' => $user->getId()])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/post/like/' . $likePost->getPost()->getId(),
            '',
            []
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }


    public function testgetFeed_NotIdenticate(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post/feed',
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetFeed_OkObjectResult(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/post/feed',
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}