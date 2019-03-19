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

/**
 * Interface VersionManagerInterface.
 *
 * as every software can have a different structure
 * a new version manager has to be implemented for that
 */
interface VersionManagerInterface
{
    /**
     * extract the versions from the given response.
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    public function extractVersions(ResponseInterface $response): array;
}
