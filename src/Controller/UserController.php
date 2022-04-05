<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("api/user")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getUser"})
     */
    public function getUserById(User $user)
    {
        return $user;
    }

    /**
     * @Get(
     *     path = "/me",
     *     name = "me_show",
     * )
     * @Rest\View(serializerGroups={"getUser"})
     */
    public function getMyProfile()
    {
        return $this->security->getUser();
    }
}
