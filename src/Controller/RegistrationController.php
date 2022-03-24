<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
/**
 * @Route("api")
 */
class RegistrationController extends AbstractFOSRestController
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return
     */
    public function registerUser(Request $request)
    {
        $user = new User();

        $user->setEmail($request->get('email'));
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $request->get('password'))
        );
        $user->setFirstName($request->get('first_name'));
        $user->setLastName($request->get('last_name'));
        $user->setBirthday(new \DateTime($request->get('birthday')));
        $user->setPromo(new \DateTime($request->get('promo')));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new Response("", Response::HTTP_CREATED);
    }
}