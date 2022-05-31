<?php

namespace App\Controller;

use App\Entity\Message;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("api/message")
 */
class MessageController extends AbstractFOSRestController
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
     *     name = "send_message"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("message", converter="fos_rest.request_body")
     */
    public function sendMessage(Message $message, ConstraintViolationList $violations)
    {
        $message->setSentBy($this->security->getUser());
        $message->setCreateAt(new \DateTime(date("d-m-Y")));
        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }
        if($message->getReceivedBy() === $this->security->getUser())
        {
            return new JsonResponse(['erreur' => 'Vous ne pouvez pas vous envoyer de message.'], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->doctrine->getManager();

        $em->persist($message);
        $em->flush();
        return new JsonResponse(['id' => $message->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/conversation",
     *     name = "conversation_show",
     * )
     * @Rest\View(StatusCode = 200)
     */
    public function getConversations()
    {
        $conversations =  $this->doctrine->getRepository(Message::class)->conversations($this->security->getUser()->getId());
        return ['conversations' => $conversations];
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "message_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     */
    public function getMessages(Request $request)
    {
        $idOtherUser = $request->attributes->get('_route_params')['id'];
        $messages =  $this->doctrine->getRepository(Message::class)->messages($this->security->getUser()->getId(), $idOtherUser);
        return ['messages' => $messages];
    }
}
