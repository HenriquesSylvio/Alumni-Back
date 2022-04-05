<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("api/post")
 */
class PostController extends AbstractFOSRestController
{
    /**
     * @Rest\Post(
     *     name = "add_post"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("post", converter="fos_rest.request_body")
     */
    public function addPost(Post $post, ManagerRegistry $doctrine, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $em = $doctrine->getManager();

        $em->persist($post);
        $em->flush();

        return new JsonResponse($post->getContent(), Response::HTTP_CREATED);
    }
}
