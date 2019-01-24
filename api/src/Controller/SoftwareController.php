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
use App\Repository\SoftwareRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SoftwareController
 * @package App\Controller
 *
 * @Route("/software")
 */
class SoftwareController extends AbstractController
{
    /**
     * @var EntityManagerInterface $client
     */
    private $entityManager;

    /**
     * @var ClientInterface $client
     */
    private $client;

    /**
     * SoftwareController constructor.
     * @param ClientInterface $client
     */
    public function __construct(EntityManagerInterface $entityManager, ClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * sets supported / maintained versions of softwares that can be defined in services.yaml
     *
     *
     * @Route("/update", methods={"GET"}, host="localhost")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateSoftwares(Request $request)
    {
        $softwares = $this->getParameter('softwares');

        /** @var SoftwareRepository $softwareRepo */
        $softwareRepo = $this->entityManager->getRepository(Software::class);

        $result = [];

        foreach ($softwares as $name => $endpoints) {
            // if software does not exist, create it
            $software = $softwareRepo->findOneByName($name);
            $manager = VersionManagerFactory::create($name);

            if (!$software) {
                $software = (new Software())->setName($name);
            }

            $versions = $software->getVersions() ?? [];

            foreach ($endpoints as $url) {
                try {
                    $response = $this->client->request('GET', $url);
                    $versions = array_merge($versions, $manager->extractVersions($response));
                } catch (GuzzleException $e) {
                }
            }

            // duplicates of versions are possible at this point -> unique items in array
            $software->setVersions(array_unique($versions));
            $this->entityManager->persist($software);
        }

        // save all
        $this->entityManager->flush();

        return new JsonResponse($softwareRepo->findAll());
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
