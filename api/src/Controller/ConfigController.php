<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-05
 * Time: 10:47
 */

namespace App\Controller;

use App\Entity\Installation;
use App\Manager\ConfigManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController
 * @package App\Controller
 *
 * @Route("/config")
 */
class ConfigController extends ApiController
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ConfigManager $configManager
     */
    private $configManager;

    /**
     * ConfigController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ConfigManager $configManager
     */
    public function __construct(EntityManagerInterface $entityManager, ConfigManager $configManager)
    {
        $this->entityManager = $entityManager;
        $this->configManager = $configManager;
    }

    /**
     * update config for specific installation
     *
     * @Route("/{hash}")
     *
     * @param $hash
     * @return Response
     */
    public function forOne($hash)
    {
        $installation = $this->entityManager->getRepository(Installation::class)->findOneByHash($hash);

        if ($installation) {
            $this->configManager
                ->setInstallations([$installation])
                ->fetchConfig()
            ;

            return new JsonResponse($installation);
        }

        return $this->createApiProblemResponse();
    }

    /**
     * update config for specific installation
     *
     * @Route("/")
     *
     * @param $hash
     * @return Response
     */
    public function forAll()
    {
        $installations = $this->entityManager->getRepository(Installation::class)->findAll();

        if ($installations) {
            $this->configManager
                ->setInstallations($installations)
                ->fetchConfig()
            ;

            return new JsonResponse($installations);
        }

        return $this->createApiProblemResponse();
    }
}
