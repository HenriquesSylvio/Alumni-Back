<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $em = $this->doctrine->getManager();

        $em->persist($event);
        $em->flush();

        return new JsonResponse($event, Response::HTTP_CREATED);
    }
}
