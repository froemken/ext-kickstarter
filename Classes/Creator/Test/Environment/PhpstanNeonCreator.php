<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Test\Environment;

use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\TestEnvInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PhpstanNeonCreator implements TestEnvCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(TestEnvInformation $testEnvInformation): void
    {
        $phpstanPath = $testEnvInformation->getBuildPath() . 'phpstan/';
        GeneralUtility::mkdir_deep($phpstanPath);
        $targetFile = $phpstanPath . 'phpstan.neon';
        if (is_file($targetFile)) {
            $testEnvInformation->getCreatorInformation()->fileExists(
                $targetFile
            );
            return;
        }
        $this->fileManager->createFile(
            $targetFile,
            $this->getTemplate(),
            $testEnvInformation->getCreatorInformation(),
        );
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
