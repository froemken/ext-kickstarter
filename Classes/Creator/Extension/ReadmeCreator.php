<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Extension;

use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;

class ReadmeCreator implements ExtensionCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(ExtensionInformation $extensionInformation): void
    {
        $this->fileManager->createOrModifyFile(
            $extensionInformation->getExtensionPath() . 'README.md',
            $this->getFileContent($extensionInformation),
            $extensionInformation->getCreatorInformation()
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
