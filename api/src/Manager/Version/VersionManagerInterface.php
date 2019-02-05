<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-24
 * Time: 16:05
 */

namespace App\Manager\Version;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface VersionManagerInterface
 * @package App\Manager\Version
 *
 *
 * as every software can have a different structure
 * a new version manager has to be implemented for that
 */
interface VersionManagerInterface
{

    /**
     * extract the versions from the given response
     *
     * @param ResponseInterface $response
     * @return array
     */
    public function extractVersions(ResponseInterface $response): array;
}
