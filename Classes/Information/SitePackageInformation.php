<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

readonly class SitePackageInformation
{
    public function __construct(
        private ExtensionInformation $extensionInformation,
        private string $type,
        private string $homepage,
        private CreatorInformation $creatorInformation = new CreatorInformation()
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHomepage(): string
    {
        return $this->homepage;
    }
}
