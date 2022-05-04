<?php

namespace App\Controller;

use App\Entity\Event;
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

        return new JsonResponse($event->getTitle(), Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "event_show_id",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\QueryParam(
     *     name="id",
     *     requirements="[0-9]",
     *     nullable=false,
     *     description="Id of the event."
     * )
     * @Rest\View(serializerGroups={"getEvent"})
     */
    public function getEventById(Event $event)
    {
        return $event;
    }

    /**
     * @Get(
     *     name = "event_show",
     * )
     * @Rest\View(serializerGroups={"getEvent"})
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
     */
    public function getEvents(ParamFetcherInterface $paramFetcher)
    {
        return $this->doctrine->getRepository('App:Event')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('past')
        );
    }
}
