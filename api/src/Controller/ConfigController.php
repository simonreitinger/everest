<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-05
 * Time: 10:47
 */

namespace App\Controller;

use App\Manager\ConfigManager;
use App\Entity\Installation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController
 * @package App\Controller
 *
 * @Route("/config/{hash}")
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
     * @param $hash
     * @param Request $request
     * @return Response
     */
    public function __invoke($hash, Request $request)
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
}
