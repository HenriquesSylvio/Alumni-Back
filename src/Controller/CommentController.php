<?php

namespace App\Controller;

use App\Entity\LikeComment;
use App\Entity\Comment;
use App\Entity\ReplyComment;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        return new JsonResponse(['id' => $comment->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "comment_show_id",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getComment"})
     */
    public function getCommentById(Request $request)
    {
        $id = $request->attributes->get('_route_params')['id'];
        $comment = $this->doctrine->getRepository(Comment::class)->searchById($id);
        return ['comment' => $comment];
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

    /**
     * @Rest\Post(
     *     name = "add_reply_comment",
     *     path = "/reply/{id}",
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("comment", converter="fos_rest.request_body")
     */
    public function postReplyComment(Comment $comment, ConstraintViolationList $violations, Request $request)
    {
        $idAnswerComment = $request->attributes->get('_route_params')['id'];
        $answerComment = $this->doctrine->getRepository(Comment::class)->find($idAnswerComment);

        $comment->setAuthor($this->security->getUser());
        $comment->setCreateAt(new \DateTime(date("d-m-Y")));
        $comment->setPost($answerComment->getPost());

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

        $replyComment = new ReplyComment();
        $replyComment->setAnswerComment($answerComment);
        $replyComment->setReplyComment($comment);
        $em->persist($replyComment);
        $em->flush();

        return new JsonResponse(['id' => $comment->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/post/{id}",
     *     name = "comment_post_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getComment"})
     */
    public function getcommentsByPost(Request $request)
    {
        $idPost = $request->attributes->get('_route_params')['id'];
        $comments = $this->doctrine->getRepository(Comment::class)->searchByPost($idPost);
        return ['comments' => $comments];
    }

    /**
     * @Get(
     *     path = "/reply/{id}",
     *     name = "comment_reply_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getComment"})
     */
    public function getReplyByComment(Request $request)
    {
        $idComment = $request->attributes->get('_route_params')['id'];
        $comments = $this->doctrine->getRepository(Comment::class)->searchByComment($idComment);
        return ['comments' => $comments];
    }

    /**
     * @Rest\Post(
     *     path = "/like",
     *     name = "add_like_comment"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("likeComment", converter="fos_rest.request_body")
     */
    public function addLikeComment(LikeComment $likeComment)
    {
        $likeComment->setUsers($this->security->getUser());
        $em = $this->doctrine->getManager();

        $em->persist($likeComment);
        $em->flush();

        return new JsonResponse(['status' => 'ok'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/like/{comment}",
     *     name = "like_comment_delete",
     *     requirements = {"comment"="\d+"}
     * )
     */
    public function deleteLikeComment(LikeComment $likeComment)
    {
        $likeComment->setUsers($this->security->getUser());
        $em = $this->doctrine->getManager();

        $em->remove($likeComment);
        $em->flush();
        return ;
    }
}
