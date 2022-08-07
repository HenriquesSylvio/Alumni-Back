<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Faculty;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("api/faculty")
 */
class FacultyController extends AbstractFOSRestController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Rest\Post(
     *     name = "add_faculty"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("faculty", converter="fos_rest.request_body")
     */
    public function addFaculty(Faculty $faculty, ConstraintViolationList $violations)
    {
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

    /**
     * @Get(
     *     name = "faculty_show"
     * )
     * @Rest\View(StatusCode = 200)
     */
    public function getFaculty()
    {
        $faculty =  $this->doctrine->getRepository(Faculty::class)->allFaculty();
//        dd($faculty);
        return ['faculty' => $faculty];
    }
}
