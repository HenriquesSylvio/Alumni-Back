<?php

namespace App\Controller;

use App\Entity\LikePost;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Security;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("api/user")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @var Security
     */
    private $security;

    private $doctrine;

    public function __construct(Security $security, ManagerRegistry $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
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
     * @Rest\Patch(
     *     path = "/{id}",
     *     name = "accept_user",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 204)
     */
    public function acceptUser(User $user)
    {
        $user->setAcceptAccount(true);

        $em = $this->doctrine->getManager();

        $em->persist($user);
        $em->flush();

        return ;
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

    /**
     * @Get(
     *     path = "/waitingValidation",
     *     name = "waiting_validation_show",
     * )
     * @Rest\View(serializerGroups={"getUser"})
     */
    public function getUserWaitingForValidation()
    {
        return $this->doctrine->getRepository('App:User')->searchUserWaitingValidation();
    }


    /**
     * @Rest\Post(
     *     path = "/subscribe",
     *     name = "add_subscribe"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("subscribe", converter="fos_rest.request_body")
     */
    public function addSubscribe(Subscribe $subscribe)
    {
        if ($subscribe->getSubscriber() == $this->security->getUser()) {
            return new JsonResponse(['erreur' => 'Vous ne pouvez vous suivre'], Response::HTTP_UNAUTHORIZED);
        }

        $subscribe->setSubscription($this->security->getUser());

        $em = $this->doctrine->getManager();

        $em->persist($subscribe);
        $em->flush();

        return new JsonResponse($subscribe, Response::HTTP_CREATED);
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/subscribe/{subscriber}",
     *     name = "subscribe_delete",
     *     requirements = {"subscriber"="\d+"}
     * )
     */
    public function deleteSubscribe(Subscribe $subscribe)
    {
        $subscribe->setSubscription($this->security->getUser());
        $em = $this->doctrine->getManager();

        $em->remove($subscribe);
        $em->flush();
        return ;
    }
}
