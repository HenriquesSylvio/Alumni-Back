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

/**
 * @Route("api/comment")
 */
class CommentController extends AbstractFOSRestController
{

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Rest\Post(
     *     name = "add_comment"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("comment", converter="fos_rest.request_body")
     */
    public function addComment(Comment $comment)
    {
        $em = $this->doctrine->getManager();

        $em->persist($comment);
        $em->flush();

        return new JsonResponse($comment->getContent(), Response::HTTP_CREATED);
    }
}
