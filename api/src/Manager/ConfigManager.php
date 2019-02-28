<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-12
 * Time: 10:16
 */

namespace App\Manager;

use App\Cache\InstallationCache;
use App\Cache\InstallationData;
use App\Client\ManagerClient;
use App\Client\InstallationCrawler;
use App\Entity\Installation;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConfigManager is responsible for updating the database records of installations
 * @package App\Config
 */
class ConfigManager
{

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var InstallationCache $cache
     */
    private $cache;

    /**
     * @var ManagerClient $client
     */
    private $client;

    /**
     * @var Installation[] $installations
     */
    private $installations;

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
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client, InstallationCache $cache)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->cache = $cache;
    }

    /**
     * @param Installation[]|Installation $installations
     * @return ConfigManager
     */
    public function setInstallations($installations): ConfigManager
    {
        if (is_array($installations)) {
            $this->installations = $installations;
        } else if($installations instanceof Installation) {
            $this->installations = [$installations];
        }

        return $this;
    }

    public function fetchConfig($output = false): bool
    {
        if ($this->installations === null) {
            throw new \BadMethodCallException('No installations found. Did you specify them using "setInstallations()"?');
        }

        /** @var Installation $installation */
        foreach ($this->installations as $installation) {
            if ($output) {
                echo 'updating ' . $installation->getCleanUrl() . PHP_EOL;
            }

            if (!$this->updateConfig($installation)) {
                return false;
            }

            if ($output) {
                echo 'updated ' . $installation->getCleanUrl() . PHP_EOL;
            }
        }

        return true;
    }

    private function updateConfig(Installation $installation): bool
    {
        $data = new InstallationData();

        foreach ($this->responses as $set => $method) {
            try {
                /** @var Psr7Response $response */
                $response = $this->client->{$method}($installation);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    // decode into array for database
                    $json = $this->client->getJsonContent($response);

                    // skip hosting providers, they are not needed for now
                    if ($set === 'setConfig') {
                        unset($json['configs']);
                    }

                    if ($set === 'setLock') {
                        $json = $this->buildLockData($json, $data->getPackages());
                    }

                    // use the keys from $responses for setting the received json
                    $data->{$set}($json);

                    continue;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        // metadata
        $metadataResponse = $this->client->homepageRequest($installation);
        (new InstallationCrawler($metadataResponse->getBody()->getContents(), $installation))->analyzeMetadata();

        $this->cache->saveInCache($installation, $data);
        $this->entityManager->persist($installation);

        return true;
    }

    /**
     * @param array $json
     * @param array $packages (composer.json contents)
     * @return array
     */
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
