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

/**
 * @Route("api")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Get(
     *     path = "/user/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getUser"})
     */
    public function getUserById(User $user)
    {
        return $user;
    }
}
