<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class CommentTest extends AbstractEndPoint
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

    public function testgetCommmentById_NotIdenticate(): void
    {
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/comment/' . $comment->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testgetCommentById_OkObjectResult(): void
    {
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/comment/' . $comment->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testdeleteComment_NoContentResult(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/comment/' . $comment->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeleteComment_AdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'user@outlook.fr'])
        ;
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/comment/' . $comment->getId(),
            "",
            [],
            true,
            true
        );
        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testdeleteComment_NotAdminConnect(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/comment/' . $comment->getId(),
            "",
            [],
            true,
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testdeleteComment_NotIdenticate(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@outlook.fr'])
        ;
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test', 'author' => $user])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_DELETE,
            '/api/comment/' . $comment->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testaddComment_CreatedResult(): void
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;
//        $comment = new Comment();
//        $comment->setContent("Ceci est un test");
//        $comment->setPost($post);
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/comment',
            '{"content": "Ceci est un test", "post" : {"id" : ' . $post->getId() .' }}',
            []
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testaddComment_NotIdenticate(): void
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/comment',
            '{"content": "Ceci est un test", "post" : {"id" : ' . $post->getId() .' }}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testpostReplyComment_NotIdenticate(): void
    {
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/comment/reply/' . $comment->getId(),
            '{"content": "Ceci est un test"}',
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testpostReplyComment_CreatedResult(): void
    {
        $comment = $this->entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;

        $response = $this->getResponseFromRequest(
            Request::METHOD_POST,
            '/api/comment/reply/' . $comment->getId(),
            '{"content": "Ceci est un test"}',
            [],
            true
        );
        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testgetCommentsByPost_OkObjectResult(): void
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/comment/post/' . $post->getId(),
            "",
            []
        );
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testgetCommentsByPost_NotIdenticate(): void
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy(['content' => 'Ceci est un test'])
        ;
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/comment/post/' . $post->getId(),
            "",
            [],
            false
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}