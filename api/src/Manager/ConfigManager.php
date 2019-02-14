<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-12
 * Time: 10:16
 */

namespace App\Manager;

use App\Client\ManagerClient;
use App\Client\WebsiteCrawler;
use App\Entity\Website;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConfigManager is responsible for updating the database records of websites
 * @package App\Config
 */
class ConfigManager
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ManagerClient $client
     */
    private $client;

    /**
     * @var Website[] $websites
     */
    private $websites;

    /**
     * map properties to client methods
     *
     * @var array $responses
     */
    private $responses = [
        'setContao' => 'serverContao',
        'setComposer' => 'serverComposer',
        'setConfig' => 'serverConfig',
        'setPhpWeb' => 'serverPhpWeb',
        'setPhpCli' => 'serverPhpCli',
        'setManager' => 'configManager',
        'setPackages' => 'packagesRoot',
        'setLock' => 'composerLock' // has to be after packagesRoot
    ];

    /**
     * ConfigManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerClient $client
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * @param Website[] $websites
     * @return ConfigManager
     */
    public function setWebsites(array $websites): ConfigManager
    {
        $this->websites = $websites;

        return $this;
    }

    public function fetchConfig(): bool
    {
        if ($this->websites === null) {
            throw new \BadMethodCallException('No websites found. Did you specify them using "setWebsites()"?');
        }

        /** @var Website $website */
        foreach ($this->websites as $website) {
            if (!$this->updateConfig($website)) {
                return false;
            }
        }

        // save all
        $this->entityManager->flush();

        return true;
    }

    private function updateConfig(Website $website): bool
    {
        foreach ($this->responses as $set => $method) {
            try {
                /** @var Psr7Response $response */
                $response = $this->client->{$method}($website);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    // decode into array for database
                    $json = $this->client->getJsonContent($response);

                    // skip hosting providers, they are not needed for now
                    if ($set === 'setConfig') {
                        unset($json['configs']);
                    }

                    if ($set === 'setLock') {
                        $json = $this->buildLockData($json, $website->getPackages());
                    }

                    // use the keys from $responses for setting the received json
                    $website->{$set}($json);

                    continue;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        // metadata
        $metadataResponse = $this->client->homepageRequest($website);
        (new WebsiteCrawler($metadataResponse->getBody()->getContents(), $website))->analyzeMetadata();

        $website->setLastUpdate();

        // add website to changes
        $this->entityManager->persist($website);

        return true;
    }

    private function buildLockData(array $json, array $packages)
    {
        $filtered = [];

        // root repository should include a "/" to filter php and ext versions
        $rootRepositories = array_filter(array_keys($packages['require']), function($name) {
            return stripos($name, '/');
        });

        $privateRepositories = array_map(function($repo) {
            return $repo['url'];
        }, $packages['repositories']);

        // sort packages in alphabetical order
        ksort($json);

        foreach ($json as $package => $data) {
            $name = $json[$package]['name'];

            $filtered[] = [
                'name' => $name,
                'version' => $json[$package]['version'],
                'versionNormalized' => $json[$package]['version_normalized'],
                'description' => $json[$package]['description'] ?? '',
                'inRoot' => $this->packageFoundInArray($name, $rootRepositories),
                'rootVersion' => $packages['require'][$name] ?? '',
                'isPrivate' => $this->packageFoundInArray($name, $privateRepositories),
            ];
        }

        usort($filtered, function($a, $b) {
            return ($a['name'] < $b['name']) ? -1 : 1;
        });

        return $filtered;
    }

    /**
     * searches $repositories for occurrences of $name
     *
     * @param $name
     * @param array $repositories
     * @return bool
     */
    private function packageFoundInArray($name, array $repositories = [])
    {
        foreach ($repositories as $repository) {
            if (stripos($repository, $name)) {
                return true;
            }
        }

        return false;
    }

}
