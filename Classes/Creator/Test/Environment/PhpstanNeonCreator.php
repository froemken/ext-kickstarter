<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Creator\Test\Environment;

use FriendsOfTYPO3\Kickstarter\Information\TestEnvInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PhpstanNeonCreator implements TestEnvCreatorInterface
{
    public function create(TestEnvInformation $testEnvInformation): void
    {
        $phpstanPath = $testEnvInformation->getBuildPath() . 'phpstan/';
        GeneralUtility::mkdir_deep($phpstanPath);

        if (!is_file($phpstanPath . 'phpstan.neon')) {
            file_put_contents(
                $phpstanPath . 'phpstan.neon',
                $this->getTemplate(),
            );
        }
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
includes:
  - phpstan-baseline.neon

parameters:
  level: 6

  inferPrivatePropertyTypeFromConstructor: true
  treatPhpDocTypesAsCertain: false

  bootstrapFiles:
    - phpstan-typo3-constants.php

  paths:
    - ../../Classes/
    - ../../Configuration/

  tmpDir: ../../.Build/.cache/phpstan/

  excludePaths:
    - '../../ext_emconf.php'
EOT;
    }
}
