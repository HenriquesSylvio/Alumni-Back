<?php
namespace App\Controller;

use App\Entity\Event;
use App\Entity\LikePost;
use App\Entity\Participate;
use App\Representation\Paginer;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * @Route("api/event")
 */
class EventController extends AbstractFOSRestController
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
     * @Rest\Post(
     *     name = "add_event"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("event", converter="fos_rest.request_body")
     */
    public function addEvent(Event $event, ConstraintViolationList $violations)
    {
        $event->setAuthor($this->security->getUser());

        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->doctrine->getManager();

        $em->persist($event);
        $em->flush();
        return new JsonResponse(['id' => $event->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "event_show_id",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getEvent"})
     */
    public function getEventById(Event $event)
    {
        return $event;
    }
//* @Rest\View(serializerGroups={"getEvent"})
    /**
     * @Get(
     *     name = "event_show",
     * )
     * @Rest\QueryParam(
     *     name="keyword",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="past",
     *     requirements="true|false",
     *     default=false,
     *     description="get event already past"
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
     * @Rest\View()
     */
    public function getEvents(ParamFetcherInterface $paramFetcher)
    {
        $events =  $this->doctrine->getRepository(Event::class)->search(
            $this->security->getUser()->getId(),
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('past'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset'),
            $paramFetcher->get('current_page')
        );
        return new Paginer($events);
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/{id}",
     *     name = "event_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deleteEvent(Event $event)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if($event->getAuthor() !== $this->security->getUser()) {
                return new JsonResponse(['erreur' => 'Vous n\'êtes pas autorisé a faire cette action'], Response::HTTP_UNAUTHORIZED);
            }
        }
        $em = $this->doctrine->getManager();
        $em->remove($event);
        $em->flush();
        return ;
    }

    /**
     * @Rest\Post(
     *     path = "/participate",
     *     name = "add_participation"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("participate", converter="fos_rest.request_body")
     */
    public function addParticipation(Participate $participate)
    {
        if(date_format($participate->getEvent()->getDate(), 'd-m-Y') < date('d-m-Y') )
        {
            return new JsonResponse(['erreur' => 'Vous ne pouvez pas participer à un événement déjà passé.'], Response::HTTP_UNAUTHORIZED);
        }
        $participate->setParticipant($this->security->getUser());
        $em = $this->doctrine->getManager();

        $em->persist($participate);
        $em->flush();

        return new JsonResponse(['status' => 'ok'], Response::HTTP_CREATED);
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/participate/{event}",
     *     name = "participation_delete",
     *     requirements = {"event"="\d+"}
     * )
     */
    public function deleteParticipation(Participate $participate)
    {
        if(date_format($participate->getEvent()->getDate(), 'd-m-Y') < date('d-m-Y') )
        {
            return new JsonResponse(['erreur' => 'Vous ne pouvez vous retirer des participants d\'un événement déjà passé.'], Response::HTTP_UNAUTHORIZED);
        }
        $participate->setParticipant($this->security->getUser());
        $em = $this->doctrine->getManager();

        $em->remove($participate);
        $em->flush();
        return ;
    }

    /**
     * @Get(
     *     path = "/participate/{id}",
     *     name = "participation_show_id",
     * )
     * @Rest\View(serializerGroups={"getParticipation"})
     */
    public function getParticipation(Request $request)
    {
        $id = $request->attributes->get('_route_params')['id'];
        $participant =  $this->doctrine->getRepository(Participate::class)->searchAllParticipant($id);
        return ['participant' => $participant];
    }
}
