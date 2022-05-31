<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("api")
 */
class AuthController extends AbstractFOSRestController
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
     *     path = "/login_check",
     *     name = "auth"
     * )
     */
    public function getTokenUser(Request $request, UserRepository $userRepository, JWTTokenManagerInterface $JWTManager)
    {
        $user = $userRepository->findOneBy([
            'email'=>$request->get('email'),
        ]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $request->get('password'))) {
            return new JsonResponse(['erreur' => 'Email ou mot passe incorrect(s)']);
        }

        if ($user->getAcceptAccount() == false) {
            return new JsonResponse(['erreur' => 'Votre compte n\'a pas été encore accepté par un admin']);
        }

        return new JsonResponse(['token' => $JWTManager->create($user)]);
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

        return new JsonResponse(['email' => $user->getEmail()], Response::HTTP_CREATED);
    }
}
