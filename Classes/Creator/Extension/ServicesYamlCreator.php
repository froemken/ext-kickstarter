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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ServicesYamlCreator implements ExtensionCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(ExtensionInformation $extensionInformation): void
    {
        // If "Classes/" path is missing, DI will fail. So, create it
        $classesPath = $extensionInformation->getExtensionPath() . 'Classes/';
        GeneralUtility::mkdir_deep($classesPath);

        $this->fileManager->createOrModifyFile($classesPath . '.gitkeep', '', $extensionInformation->getCreatorInformation());

        $servicesYamlPath = $extensionInformation->getExtensionPath() . 'Configuration/';
        GeneralUtility::mkdir_deep($servicesYamlPath);

        $this->fileManager->createOrModifyFile(
            $servicesYamlPath . 'Services.yaml',
            sprintf($this->getTemplate(), $extensionInformation->getNamespacePrefix()),
            $extensionInformation->getCreatorInformation()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
services:
  _defaults:
    autowire: true
    autoconfigure: true
    public: false

  %s:
    resource: '../Classes/*'
    exclude:
    - '../Classes/Domain/Model/*'
EOT;
    }
}
