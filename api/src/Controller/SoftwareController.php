<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-24
 * Time: 15:12
 */

namespace App\Controller;

use App\Entity\Software;
use App\Factory\VersionManagerFactory;
use App\Manager\SoftwareManager;
use App\Repository\SoftwareRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SoftwareController
 * @package App\Controller
 *
 * @Route("/software")
 */
class SoftwareController extends ApiController
{

    /**
     * @var SoftwareManager $softwareManager
     */
    private $softwareManager;

    /**
     * @var EntityManagerInterface $client
     */
    private $entityManager;



    /**
     * SoftwareController constructor.
     * @param ClientInterface $client
     */
    public function __construct(
        SoftwareManager $softwareManager,
        EntityManagerInterface $entityManager)
    {
        $this->softwareManager = $softwareManager;
        $this->entityManager = $entityManager;
    }

    /**
     * sets supported / maintained versions of softwares that can be defined in services.yaml
     *
     *
     * @Route("/update", methods={"GET"})
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateSoftwares()
    {
        return new JsonResponse($this->softwareManager->update());
    }

    /**
     * @Route(methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getSoftwares()
    {
        $softwares = $this->entityManager->getRepository(Software::class)->findAll() ?? [];

        return new JsonResponse($softwares);
    }

    /**
     * @Route("/{name}/versions", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getVersionsByName($name)
    {
        $software = $this->entityManager->getRepository(Software::class)->findOneByName($name);

        return new JsonResponse($software->getVersions());
    }
}
