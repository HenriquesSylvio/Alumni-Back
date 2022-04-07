<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("api/post")
 */
class PostController extends AbstractFOSRestController
{

    /**
     * @var Security
     */
    private $security;

    private $doctrine;

    public function __construct(Security $security, ManagerRegistry $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
    }

    /**
     * @Rest\Post(
     *     name = "add_post"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("post", converter="fos_rest.request_body")
     */
    public function addPost(Post $post, ConstraintViolationList $violations)
    {
        $post->setAuthor($this->security->getUser());
        $post->setCreateAt(new \DateTime(date("d-m-Y")));

        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->doctrine->getManager();

        $em->persist($post);
        $em->flush();

        return new JsonResponse($post->getContent(), Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "post_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getPost"})
     */
    public function getPostById(Post $post)
    {
        return $post;
    }

    /**
     * @Get(
     *     path = "/user/{id}",
     *     name = "post_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getPost"})
     */
    public function getPostByUser()
    {
        return $this->doctrine->getRepository('App:Post')->searchByUser(1);
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/{id}",
     *     name = "post_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deletePost(Post $post)
    {
        if($post->getAuthor() !== $this->security->getUser())
        {
            return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_BAD_REQUEST);
        }
        $em = $this->doctrine->getManager();
        $em->remove($post);
        $em->flush();
        return ;
    }
}
