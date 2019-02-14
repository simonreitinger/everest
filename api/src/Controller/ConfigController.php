<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-05
 * Time: 10:47
 */

namespace App\Controller;

use App\Manager\ConfigManager;
use App\Entity\Website;
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
     * load the config initially
     *
     * @param $hash
     * @param Request $request
     * @return Response
     */
    public function __invoke($hash, Request $request)
    {
        $website = $this->entityManager->getRepository(Website::class)->findOneByHash($hash);

        if ($website) {
            $this->configManager
                ->setWebsites([$website])
                ->fetchConfig()
            ;

            return new JsonResponse($website);
        }

        return $this->createApiProblemResponse();
    }
}
