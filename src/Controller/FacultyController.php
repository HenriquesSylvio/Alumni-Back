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
    public function addFaculty(Faculty $faculty)
    {
        $em = $this->doctrine->getManager();

        $em->persist($faculty);
        $em->flush();

        return new JsonResponse(['id' => $faculty->getId()], Response::HTTP_CREATED);
    }
}
