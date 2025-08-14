<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Creator\Extension;

use FriendsOfTYPO3\Kickstarter\Information\ExtensionInformation;

class ReadmeCreator implements ExtensionCreatorInterface
{
    public function create(ExtensionInformation $extensionInformation): void
    {
        file_put_contents(
            $extensionInformation->getExtensionPath() . 'README.md',
            $this->getFileContent($extensionInformation),
        );
    }

    private function getFileContent(ExtensionInformation $extensionInformation): string
    {
        return sprintf(
            $this->getTemplate(),
            $extensionInformation->getTitle(),
            $extensionInformation->getDescription()
        );
    }

    private function getTemplate(): string
    {
        return <<<'PHP'
# %s

%s
PHP;
    }
}
