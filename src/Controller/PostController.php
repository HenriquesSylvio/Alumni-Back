<?php

namespace App\Controller;

use App\Entity\LikePost;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;

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
     * @Rest\Post(
     *     path = "/like",
     *     name = "add_like_post"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("likePost", converter="fos_rest.request_body")
     */
    public function addLikePost(LikePost $likePost)
    {
        $likePost->setUsers($this->security->getUser());

        $em = $this->doctrine->getManager();

        $em->persist($likePost);
        $em->flush();

        return new JsonResponse($likePost, Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "post_show_id",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\QueryParam(
     *     name="id",
     *     requirements="[0-9]",
     *     nullable=false,
     *     description="Id of the post."
     * )
     * @Rest\View(serializerGroups={"getPost"})
     */
    public function getPostById(Request $request)
    {
        $id = $request->attributes->get('_route_params')['id'];
        return $this->doctrine->getRepository('App:Post')->searchById($id);
    }

    /**
     * @Get(
     *     name = "post_show",
     * )
     * @Rest\View(serializerGroups={"getPost"})
     * @Rest\QueryParam(
     *     name="keyword",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     */
    public function getPosts(ParamFetcherInterface $paramFetcher)
    {
        return $this->doctrine->getRepository('App:Post')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
        );
    }

    /**
     * @Get(
     *     path = "/user/{id}",
     *     name = "post_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getPost"})
     */
    public function getPostsByUser(Request $request)
    {
        $idAuthor = $request->attributes->get('_route_params')['id'];
        return $this->doctrine->getRepository('App:Post')->searchByUser($idAuthor);
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
        if (!in_array("ROLE_ADMIN", $this->security->getUser()->getRoles())) {
            if($post->getAuthor() !== $this->security->getUser()) {
                return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_UNAUTHORIZED);
            }
        }
        $em = $this->doctrine->getManager();
        $em->remove($post);
        $em->flush();
        return ;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/like/{post}/{user}",
     *     name = "like_post_delete",
     *     requirements = {"post"="\d+", "user"="\d+"}
     * )
     */
    public function deleteLikePost(LikePost $likePost)
    {
        if($likePost->getUsers() !== $this->security->getUser())
        {
            return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_UNAUTHORIZED);
        }
        $em = $this->doctrine->getManager();

        $em->remove($likePost);
        $em->flush();
        return ;
    }
}
