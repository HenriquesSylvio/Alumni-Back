<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("api")
 */
class RegistrationController extends AbstractFOSRestController
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Rest\Post(
     *     path = "/register",
     *     name = "register"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function registerUser(User $user, ManagerRegistry $doctrine, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $em = $doctrine->getManager();

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPassword())
        );
        $user->setAcceptAccount(false);

        $em->persist($user);
        $em->flush();

        return new JsonResponse($user->getEmail(), Response::HTTP_CREATED);
    }
}