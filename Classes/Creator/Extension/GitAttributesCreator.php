<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Extension;

use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;

class GitAttributesCreator implements ExtensionCreatorInterface
{
    public function create(ExtensionInformation $extensionInformation): void
    {
        file_put_contents(
            $extensionInformation->getExtensionPath() . '.gitattributes',
            $this->getTemplate(),
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
