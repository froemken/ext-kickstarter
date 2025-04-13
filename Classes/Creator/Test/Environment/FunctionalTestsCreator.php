<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Test\Environment;

use StefanFroemken\ExtKickstarter\Information\TestEnvInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FunctionalTestsCreator implements TestEnvCreatorInterface
{
    public function create(TestEnvInformation $testEnvInformation): void
    {
        $phpunitPath = $testEnvInformation->getBuildPath() . 'phpunit/';
        GeneralUtility::mkdir_deep($phpunitPath);

        if (!is_file($phpunitPath . 'FunctionalTests.xml')) {
            file_put_contents(
                $phpunitPath . 'FunctionalTests.xml',
                $this->getTemplate(),
            );
        }
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?xml version="1.0"?>

<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.1/phpunit.xsd"
         backupGlobals="true"
         beStrictAboutTestsThatDoNotTestAnything="false"
         bootstrap="FunctionalTestsBootstrap.php"
         cacheResult="false"
         colors="true"
         failOnRisky="true"
         failOnWarning="true"
>
    <testsuites>
        <testsuite name="Functional tests">
            <!--
                This path either needs an adaption in extensions, or an extension's
                test location path needs to be given to phpunit.
            -->
            <directory>../../Tests/Functional/</directory>
        </testsuite>
    </testsuites>
    <php>
        <ini name="display_errors" value="1"/>
        <env name="TYPO3_CONTEXT" value="Testing"/>
    </php>
</phpunit>
EOT;
    }
}
