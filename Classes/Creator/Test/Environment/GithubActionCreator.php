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

class GithubActionCreator implements TestEnvCreatorInterface
{
    public function create(TestEnvInformation $testEnvInformation): void
    {
        $githubActionPath = $testEnvInformation->getExtensionInformation()->getExtensionPath() . '.github/workflows/';
        GeneralUtility::mkdir_deep($githubActionPath);

        if (!is_file($githubActionPath . 'ci.yml')) {
            file_put_contents(
                $githubActionPath . 'ci.yml',
                $this->getTemplate(),
            );
        }
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
name: Tests

on: [pull_request]

jobs:
  testing:
    name: Testing

    runs-on: ubuntu-latest

    strategy:
      fail-fast: true

      matrix:
        php:
          - '8.2'
          - '8.3'

    steps:
      - name: 'Checkout'
        uses: actions/checkout@v4

      - name: 'Lint PHP'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -s lint

      - name: 'Install testing system'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -s composerUpdate

      - name: 'Composer validate'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -s composerValidate

      - name: 'Composer normalize'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -s composerNormalize -n

      - name: 'CGL'
        run: Build/Scripts/runTests.sh -n -p ${{ matrix.php }} -s cgl

      - name: 'phpstan'
        run: Build/Scripts/runTests.sh -n -p ${{ matrix.php }} -s phpstan

      - name: 'Execute unit tests'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -s unit

      - name: 'Execute functional tests'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -d mysql -s functional

      - name: 'Execute functional tests'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -d mariadb -s functional

      - name: 'Execute functional tests'
        run: Build/Scripts/runTests.sh -p ${{ matrix.php }} -d postgres -s functional
EOT;
    }
}
