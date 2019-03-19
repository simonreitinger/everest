<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Manager\Version;

use Psr\Http\Message\ResponseInterface;

class PhpVersionManager implements VersionManagerInterface
{
    /**
     * extract the versions from the given response body ($json).
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function extractVersions(ResponseInterface $response): array
    {
        return array_keys(json_decode($response->getBody()->getContents(), true));
    }
}
