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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ServicesYamlCreator implements ExtensionCreatorInterface
{
    public function create(ExtensionInformation $extensionInformation): void
    {
        // If "Classes/" path is missing, DI will fail. So, create it
        $classesPath = $extensionInformation->getExtensionPath() . 'Classes/';
        GeneralUtility::mkdir_deep($classesPath);

        file_put_contents($classesPath . '.gitkeep', '');

        $servicesYamlPath = $extensionInformation->getExtensionPath() . 'Configuration/';
        GeneralUtility::mkdir_deep($servicesYamlPath);

        file_put_contents(
            $servicesYamlPath . 'Services.yaml',
            sprintf($this->getTemplate(), $extensionInformation->getNamespacePrefix()),
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
