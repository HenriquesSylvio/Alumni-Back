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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
            'username'=>$request->get('username'),
        ]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $request->get('password'))) {
            return new JsonResponse(['erreur' => 'Nom d\'utilisateur ou mot passe incorrect(s)'], Response::HTTP_BAD_REQUEST);
        }

        if ($user->getAcceptAccount() == false) {
            return new JsonResponse(['erreur' => 'Votre compte n\'a pas été encore accepté par un admin'], Response::HTTP_BAD_REQUEST);
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
    public function registerUser(User $user, ManagerRegistry $doctrine,ValidatorInterface $validator)
    {

        $errors = $validator->validate($user,null,  ['register']);
        if (count($errors)) {
            foreach($errors as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $maxPromo = date('Y', strtotime(date('Y'). ' + 3 years'));
        if (!($user->getPromo() >= 2017 && $user->getPromo() <= $maxPromo)) {
            return new JsonResponse(['erreur' => 'L\'année de la promo ne peut pas être inférieure à 2017 et à supérieure à  ' . $maxPromo], Response::HTTP_BAD_REQUEST);
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
