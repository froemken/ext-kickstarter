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

class ComposerJsonCreator implements ExtensionCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(ExtensionInformation $extensionInformation): void
    {
        $this->fileManager->createOrModifyFile(
            $extensionInformation->getExtensionPath() . 'composer.json',
            $this->getFileContent($extensionInformation),
            $extensionInformation->getCreatorInformation()
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
                'typo3/cms-core' => '^13.4',
            ],
            'autoload' => [
                'psr-4' => [
                    $extensionInformation->getNamespaceForAutoload() => 'Classes/',
                ],
            ],
            'config' => [
                'allow-plugins' => [
                    'typo3/class-alias-loader' => true,
                    'typo3/cms-composer-installers' => true,
                ],
                'sort-packages' => true,
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
