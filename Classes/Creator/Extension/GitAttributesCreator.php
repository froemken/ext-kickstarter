<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Creator\Extension;

use FriendsOfTYPO3\Kickstarter\Creator\FileManager;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionInformation;

class GitAttributesCreator implements ExtensionCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(ExtensionInformation $extensionInformation): void
    {
        $this->fileManager->createOrModifyFile(
            $extensionInformation->getExtensionPath() . '.gitattributes',
            $this->getTemplate(),
            $extensionInformation->getCreatorInformation()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
/.editorconfig export-ignore
/.gitattributes export-ignore
/.gitignore export-ignore
/.phpstorm.meta.php export-ignore
EOT;
    }
}
