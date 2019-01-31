<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-22
 * Time: 16:31
 */

namespace App\Controller;

use App\Client\ManagerClient;
use App\Client\WebsiteCrawler;
use App\Entity\Website;
use App\HttpKernel\ApiProblemResponse;
use Crell\ApiProblem\ApiProblem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfigController
 * @package App\Controller
 *
 * responsible for updating configuration information
 *
 * @Route("/config/{hash}", name="website_config", methods={"GET"})
 */
class ConfigController extends AbstractController
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ManagerClient $guzzle
     */
    private $client;

    /**
     * ConfigController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * fetch the current config data
     *
     * @param $hash
     * @return JsonResponse|ApiProblemResponse
     */
    public function __invoke($hash)
    {
        /** @var Website $website */
        $website = $this->entityManager->getRepository(Website::class)->findOneBy(['hash' => $hash]);

        if (!$website) {
            return new ApiProblemResponse(
                (new ApiProblem('Requested resource does not exist'))->setStatus(Response::HTTP_BAD_REQUEST)
            );
        }

        $this->updateConfig($website);

        return new JsonResponse(['success' => true]);
    }

    public function updateConfig(Website $website): void
    {
        // perform the configuration requests
        $responses = [
            'setContao' => $this->client->serverContao($website),
            'setComposer' => $this->client->serverComposer($website),
            'setConfig' => $this->client->serverConfig($website),
            'setPhpWeb' => $this->client->serverPhpWeb($website),
            'setPhpCli' => $this->client->serverPhpCli($website),
            'setManager' => $this->client->configManager($website),
            'setPackages' => $this->client->packagesRoot($website)
        ];

        foreach ($responses as $set => $response) {
            if ($response->getStatusCode() === Response::HTTP_OK) {
                // decode into array for database
                $json = json_decode($response->getBody()->getContents(), true);

                // skip hosting providers, they are not needed for now
                if ($set === 'setConfig') {
                    unset($json['configs']);
                }

                $website->{$set}($json);
            }
        }

        // metadata
        $metadataResponse = $this->client->homepageRequest($website);
        (new WebsiteCrawler($metadataResponse->getBody()->getContents(), $website))->analyzeMetadata();

        $website->setLastUpdate();
        $this->entityManager->persist($website);
        $this->entityManager->flush();
    }
}
