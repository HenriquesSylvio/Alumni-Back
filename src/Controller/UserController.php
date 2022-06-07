<?php

namespace App\Controller;

use App\Entity\LikePost;
use App\Entity\Subscribe;
use App\Entity\User;
use App\Representation\Paginer;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcherInterface;
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
        if(is_null($user)) {
            return new JsonResponse(['erreur' => 'Aucun utilisateur n\'a été trouvé'], Response::HTTP_NOT_FOUND);
        }
        if(!$user[0]->getAcceptAccount()){
            return new JsonResponse(['erreur' => 'Vous ne pouvez pas afficher le profil d\'un utilisateur qui n\'a pas encore été accepté.'], Response::HTTP_UNAUTHORIZED);
        }
        return $user[0];
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
        return $this->doctrine->getRepository(User::class)->searchById($this->security->getUser()->getId())[0];
    }

    /**
     * @Get(
     *     name = "users_show",
     * )
     * @Rest\View()
     * @Rest\QueryParam(
     *     name="keyword",
     *     nullable=true,
     *     description="The user to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\QueryParam(
     *     name="current_page",
     *     requirements="\d+",
     *     default="1",
     *     description="The current page"
     * )
     */
    public function getUsers(ParamFetcherInterface $paramFetcher)
    {
        $users = $this->doctrine->getRepository(User::class)->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset'),
            $paramFetcher->get('current_page')
        );
        return new Paginer($users);
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
        if ($subscribe->getSubscriber() === $this->security->getUser()) {
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
        if ((!$this->isGranted('ROLE_ADMIN') and $user !== $this->security->getUser()) Or in_array('ROLE_SUPER_ADMIN', $user->getRoles()) or (in_array('ROLE_ADMIN', $user->getRoles()) And !$this->isGranted('ROLE_SUPER_ADMIN'))) {
                return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_UNAUTHORIZED);
        }
        $em = $this->doctrine->getManager();
        $em->remove($user);
        $em->flush();
        return ;
    }
}
