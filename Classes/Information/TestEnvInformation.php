<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

class TestEnvInformation
{
    private const BUILD_PATH = 'Build/';

    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getBuildPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::BUILD_PATH;
    }
}
