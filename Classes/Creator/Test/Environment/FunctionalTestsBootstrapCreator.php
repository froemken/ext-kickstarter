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

class FunctionalTestsBootstrapCreator implements TestEnvCreatorInterface
{
    public function create(TestEnvInformation $testEnvInformation): void
    {
        $phpunitPath = $testEnvInformation->getBuildPath() . 'phpunit/';
        GeneralUtility::mkdir_deep($phpunitPath);

        if (!is_file($phpunitPath . 'FunctionalTestsBootstrap.php')) {
            file_put_contents(
                $phpunitPath . 'FunctionalTestsBootstrap.php',
                $this->getTemplate(),
            );
        }
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

use TYPO3\TestingFramework\Core\Testbase;

(static function () {
    $testbase = new Testbase();
    $testbase->defineOriginalRootPath();
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/tests');
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/transient');
})();
EOT;
    }
}
