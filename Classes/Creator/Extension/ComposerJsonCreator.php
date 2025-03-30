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

class ComposerJsonCreator implements ExtensionCreatorInterface
{
    public function create(ExtensionInformation $extensionInformation): void
    {
        file_put_contents(
            $extensionInformation->getExtensionPath() . 'composer.json',
            $this->getFileContent($extensionInformation),
        );
    }

    private function getFileContent(ExtensionInformation $extensionInformation): string
    {
        $composerConfig = [
            'name' => $extensionInformation->getComposerPackageName(),
            'description' => $extensionInformation->getDescription(),
            'type' => 'typo3-cms-extension',
            'license' => 'GPL-2.0-or-later',
            'authors' => [
                [
                    'name' => $extensionInformation->getAuthor(),
                    'email' => $extensionInformation->getAuthorEmail(),
                ],
            ],
            'require' => [
                'typo3/cms-core' => '^12.4',
            ],
            'autoload' => [
                'psr-4' => [
                    $extensionInformation->getNamespaceForAutoload() => 'Classes/',
                ],
            ],
            'extra' => [
                'typo3/cms' => [
                    'extension-key' => $extensionInformation->getExtensionKey(),
                ],
            ],
        ];

        return json_encode($composerConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
