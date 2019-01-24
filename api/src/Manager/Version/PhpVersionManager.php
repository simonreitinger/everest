<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-24
 * Time: 16:03
 */

namespace App\Manager\Version;

use Psr\Http\Message\ResponseInterface;

class PhpVersionManager implements VersionManagerInterface
{
    /**
     * extract the versions from the given response body ($json)
     *
     * @param ResponseInterface $response
     * @return array
     */
    public function extractVersions(ResponseInterface $response): array
    {
        return array_keys(json_decode($response->getBody()->getContents(), true));
    }
}
