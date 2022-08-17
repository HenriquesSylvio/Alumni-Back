<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use App\Representation\Paginer;
/**
 * @Route("api/job")
 */
class JobController extends AbstractFOSRestController
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
     *     name = "add_job"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("job", converter="fos_rest.request_body")
     */
    public function addJob(Job $job, ConstraintViolationList $violations)
    {
        $job->setAuthor($this->security->getUser());
        $job->setCreateAt(new \DateTime(date("d-m-Y")));

        if (count($violations)) {
            foreach($violations as $error)
            {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['erreur' => $errorArray], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->doctrine->getManager();

        $em->persist($job);
        $em->flush();

        return new JsonResponse(['id' => $job->getId()], Response::HTTP_CREATED);
    }

    /**
     * @Get(
     *     path = "/{id}",
     *     name = "job_show_id",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(serializerGroups={"getJob})
     */
    public function getJobById(Request $request)
    {
        $id = $request->attributes->get('_route_params')['id'];
        $job = $this->doctrine->getRepository(Job::class)->searchById($id);
        return $job;
    }

    /**
     * @Get(
     *     name = "job_show",
     * )
     * @Rest\View()
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
    public function getJobs(ParamFetcherInterface $paramFetcher)
    {
        $jobs = $this->doctrine->getRepository(Job::class)->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset'),
            $paramFetcher->get('current_page')
        );
        return new Paginer($jobs);
    }
}
