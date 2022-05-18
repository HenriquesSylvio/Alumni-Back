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
    public function getUserById(Request $request)
    {
        $idUser = $request->attributes->get('_route_params')['id'];
        $user =  $this->doctrine->getRepository(User::class)->searchById($idUser);
        return ['user' => $user];
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
        $user =  $this->doctrine->getRepository(User::class)->searchById($this->security->getUser()->getId());
        return ['user' => $user];
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
        $users = $this->doctrine->getRepository(User::class)->searchUserWaitingValidation();
        return ['users' => $users];
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
            return new JsonResponse(['erreur' => 'Vous ne pouvez pas vous suivre.'], Response::HTTP_UNAUTHORIZED);
        }
        if ($subscribe->getSubscriber()->getAcceptAccount() == false) {
            return new JsonResponse(['erreur' => 'Vous ne pouvez pas suivre un utilisateur qui n\'a pas encore été accepté.'], Response::HTTP_UNAUTHORIZED);
        }

        $subscribe->setSubscription($this->security->getUser());

        $em = $this->doctrine->getManager();

        $em->persist($subscribe);
        $em->flush();

        return new JsonResponse(['status' => 'ok'], Response::HTTP_CREATED);
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

    /**
     * @Get(
     *     path = "/subscriber/{id}",
     *     name = "subcriber",
     * )
     * @Rest\View(serializerGroups={"getSubscriber"})
     */
    public function getSubcriber(Request $request)
    {
        $id = $request->attributes->get('_route_params')['id'];
        $subscriber =  $this->doctrine->getRepository(Subscribe::class)->searchAllSubscriber($id);
        return ['users' => $subscriber];
    }

    /**
     * @Get(
     *     path = "/subscription/{id}",
     *     name = "subscription_show_id",
     * )
     * @Rest\View(serializerGroups={"getSubscriber"})
     */
    public function getSubscription(Request $request)
    {
        $id = $request->attributes->get('_route_params')['id'];
        $subscription =  $this->doctrine->getRepository(Subscribe::class)->searchAllSubscription($id);
        return ['users' => $subscription];
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/{id}",
     *     name = "user_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deleteUser(User $user)
    {
        if (!in_array("ROLE_ADMIN", $this->security->getUser()->getRoles())) {
            if($user !== $this->security->getUser()) {
                return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_UNAUTHORIZED);
            }
        }
        $em = $this->doctrine->getManager();
        $em->remove($user);
        $em->flush();
        return ;
    }
}
