<?php

namespace App\Controller;


use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("api/comment")
 */
class CommentController extends AbstractFOSRestController
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
     *     name = "add_comment"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("comment", converter="fos_rest.request_body")
     */
    public function addComment(Comment $comment, ConstraintViolationList $violations)
    {
        $comment->setAuthor($this->security->getUser());
        $comment->setCreateAt(new \DateTime(date("d-m-Y")));

        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->doctrine->getManager();

        $em->persist($comment);
        $em->flush();

        return new JsonResponse($comment->getContent(), Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "comment_show_id",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getComment"})
     */
    public function getCommentById(Comment $comment)
    {
        return $comment;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/{id}",
     *     name = "comment_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deleteComment(Comment $comment)
    {
        if (!in_array("ROLE_ADMIN", $this->security->getUser()->getRoles())) {
            if($comment->getAuthor() !== $this->security->getUser()) {
                return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_UNAUTHORIZED);
            }
        }
        $em = $this->doctrine->getManager();
        $em->remove($comment);
        $em->flush();
        return ;
    }

}
