<?php

namespace App\Controller;

use App\Entity\Faculty;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @Route("api/faculty")
 */
class FacultyController extends AbstractController
{

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Rest\Post(
     *     path = "/create",
     *     name = "add_faculty"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("faculty", converter="fos_rest.request_body")
     */
    public function addFaculty(Faculty $faculty, ConstraintViolationList $violations)
    {

        if ((!$this->isGranted('ROLE_ADMIN') or !$this->isGranted('ROLE_SUPER_ADMIN'))) {
            return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_UNAUTHORIZED);
        }

        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->doctrine->getManager();

        $em->persist($faculty);
        $em->flush();

        return new JsonResponse(['id' => $faculty->getId()], Response::HTTP_CREATED);
    }
}
