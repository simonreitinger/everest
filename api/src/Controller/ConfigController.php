<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-05
 * Time: 10:47
 */

namespace App\Controller;

use App\Entity\Website;
use App\HttpKernel\ApiProblemResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
     * ConfigController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * load the config initially
     *
     * @param $hash
     * @param Request $request
     * @param KernelInterface $kernel
     * @return JsonResponse|ApiProblemResponse
     */
    public function __invoke($hash, Request $request, KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        /** @var Website $website */
        $website = $this->entityManager->getRepository(Website::class)->findOneByHash($hash);

        if (!$website) {
            $this->createApiProblemResponse('', Response::HTTP_BAD_REQUEST);
        }

        $input = new ArrayInput([
            'command' => 'everest:update-config',
            'url' => $website->getUrl()
        ]);

        try {
            $application->run($input, new NullOutput());
        } catch (\Exception $e) {
            $this->createApiProblemResponse('', Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => true]);
    }
}
