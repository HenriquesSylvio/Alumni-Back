<?php

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationList;


/**
 * @Route("api/tag")
 */
class TagController extends AbstractFOSRestController
{

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Rest\Post(
     *     name = "add_tag"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("tag", converter="fos_rest.request_body")
     */
    public function addTag(Tag $tag, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }
        $em = $this->doctrine->getManager();
        $em->persist($tag);
        $em->flush();

        return new JsonResponse($tag, Response::HTTP_CREATED);
    }

}
