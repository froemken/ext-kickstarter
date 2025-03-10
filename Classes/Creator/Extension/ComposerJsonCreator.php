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
use StefanFroemken\ExtKickstarter\Traits\GetExtensionPathTrait;

class ComposerJsonCreator implements ExtensionCreatorInterface
{
    use GetExtensionPathTrait;

    public function create(ExtensionInformation $extensionInformation): void
    {
        $extensionPath = $this->getExtensionPath($extensionInformation->getExtensionKey());

        file_put_contents(
            $extensionPath . 'composer.json',
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
                ]
            ],
            'require' => [
                'typo3/cms-core' => '^12.4'
            ],
            'autoload' => [
                'psr-4' => [
                    $extensionInformation->getNamespacePrefix() => 'Classes/'
                ]
            ]
        ];

        return json_encode($composerConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
