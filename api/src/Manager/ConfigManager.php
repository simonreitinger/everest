<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-12
 * Time: 10:16
 */

namespace App\Manager;

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
    public function __construct(EntityManagerInterface $entityManager, ManagerClient $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
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

    public function fetchConfig(): bool
    {
        if ($this->installations === null) {
            throw new \BadMethodCallException('No installations found. Did you specify them using "setInstallations()"?');
        }

        /** @var Installation $installation */
        foreach ($this->installations as $installation) {
            if (!$this->updateConfig($installation)) {
                return false;
            }
        }

        // save all
        $this->entityManager->flush();

        return true;
    }

    private function updateConfig(Installation $installation): bool
    {
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
                        $json = $this->buildLockData($json, $installation->getPackages());
                    }

                    // use the keys from $responses for setting the received json
                    $installation->{$set}($json);

                    continue;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        // metadata
        $metadataResponse = $this->client->homepageRequest($installation);
        (new InstallationCrawler($metadataResponse->getBody()->getContents(), $installation))->analyzeMetadata();

        $installation->setLastUpdate();

        // add installation to changes
        $this->entityManager->persist($installation);

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
