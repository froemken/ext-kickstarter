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

class ComposerJsonCreator implements TestEnvCreatorInterface
{
    public function create(TestEnvInformation $testEnvInformation): void
    {
        $composerJsonFilepath = $testEnvInformation->getExtensionInformation()->getExtensionPath() . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonFilepath), true);

        file_put_contents(
            $composerJsonFilepath,
            $this->updateComposerJson($composerConfig),
        );
    }

    private function updateComposerJson(array $composerConfig): string
    {
        if (!isset($composerConfig['require-dev']['ergebnis/composer-normalize'])) {
            $composerConfig['require-dev']['ergebnis/composer-normalize'] = '^2.44';
        }

        if (!isset($composerConfig['require-dev']['phpstan/phpstan'])) {
            $composerConfig['require-dev']['phpstan/phpstan'] = '^1.10';
        }

        if (!isset($composerConfig['require-dev']['phpunit/phpunit'])) {
            $composerConfig['require-dev']['phpunit/phpunit'] = '^10.5';
        }

        if (!isset($composerConfig['require-dev']['typo3/coding-standards'])) {
            $composerConfig['require-dev']['typo3/coding-standards'] = '^0.8';
        }

        if (!isset($composerConfig['require-dev']['typo3/testing-framework'])) {
            $composerConfig['require-dev']['typo3/testing-framework'] = '^8.2';
        }

        ksort($composerConfig['require-dev']);

        if (!isset($composerConfig['config']['allow-plugins']['ergebnis/composer-normalize'])) {
            $composerConfig['config']['allow-plugins']['ergebnis/composer-normalize'] = true;
        }

        ksort($composerConfig['config']['allow-plugins']);

        if (!isset($composerConfig['config']['bin-dir'])) {
            $composerConfig['config']['bin-dir'] = '.Build/bin';
        }

        if (!isset($composerConfig['config']['vendor-dir'])) {
            $composerConfig['config']['vendor-dir'] = '.Build/vendor';
        }

        ksort($composerConfig['config']);

        if (!isset($composerConfig['extra']['typo3/cms']['app-dir'])) {
            $composerConfig['extra']['typo3/cms']['app-dir'] = '.Build';
        }

        if (!isset($composerConfig['extra']['typo3/cms']['web-dir'])) {
            $composerConfig['extra']['typo3/cms']['web-dir'] = '.Build/public';
        }

        ksort($composerConfig['extra']['typo3/cms']);

        return json_encode($composerConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
