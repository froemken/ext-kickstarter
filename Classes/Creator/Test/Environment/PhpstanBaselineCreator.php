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

class PhpstanBaselineCreator implements TestEnvCreatorInterface
{
    public function create(TestEnvInformation $testEnvInformation): void
    {
        $phpstanPath = $testEnvInformation->getBuildPath() . 'phpstan/';
        GeneralUtility::mkdir_deep($phpstanPath);

        if (!is_file($phpstanPath . 'phpstan-baseline.neon')) {
            file_put_contents(
                $phpstanPath . 'phpstan-baseline.neon',
                $this->getTemplate(),
            );
        }
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
parameters:
  # Ignore specific errors:
  ignoreErrors:
EOT;
    }
}
