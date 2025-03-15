<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait ExtensionInformationTrait
{
    /**
     * Collects data from composer.json to build ExtensionInformation object.
     * Can only be called for existing extensions with existing composer.json file
     */
    private function getExtensionInformation(string $extensionKey): ExtensionInformation
    {
        $extensionPath = $this->getExtensionPath($extensionKey);
        $composerManifestPath = $extensionPath . 'composer.json';

        if (!is_file($composerManifestPath)) {
            throw new \InvalidArgumentException('Extension does not have a composer.json file', 1741623621);
        }

        $composerManifest = json_decode((file_get_contents($composerManifestPath) ?: ''), true) ?? [];
        $extEmConfManifest = $this->getExtEmConf($extensionKey, $extensionPath);

        return new ExtensionInformation(
            $extensionKey,
            $composerManifest['name'] ?? '',
            $extEmConfManifest['title'] ?? '',
            $composerManifest['description'] ?? '',
            $extEmConfManifest['version'] ?? '0.0.0',
            $extEmConfManifest['category'] ?? 'plugin',
            $extEmConfManifest['state'] ?? 'alpha',
            $extEmConfManifest['author'] ?? '',
            $extEmConfManifest['author_email'] ?? '',
            $extEmConfManifest['author_company'] ?? '',
            key($composerManifest['autoload']['psr-4']) ?? '',
            $extensionPath
        );
    }

    private function getExtEmConf(string $extensionKey, string $extensionPath): array
    {
        $_EXTKEY = $extensionKey;
        $path = $extensionPath . 'ext_emconf.php';
        $EM_CONF = null;
        if (@file_exists($path)) {
            include $path;
            if (is_array($EM_CONF[$_EXTKEY])) {
                return $EM_CONF[$_EXTKEY];
            }
        }

        return [];
    }

    /**
     * It returns the target directory (incl. ending slash) where the extension will be created or resides
     */
    private function getExtensionPath(string $extensionKey): string
    {
        if ($extensionKey === '') {
            throw new \InvalidArgumentException('Extension key must not be empty', 1741623620);
        }

        return sprintf(
            '%s/%s/%s/',
            Environment::getPublicPath(),
            'typo3temp/ext-kickstarter',
            $extensionKey
        );
    }

    private function createExtensionPath(
        string $extensionKey,
        bool $removePreviousExportDirectoryIfExists = false
    ): string {
        $extensionPath = $this->getExtensionPath($extensionKey);

        if ($removePreviousExportDirectoryIfExists && is_dir($extensionPath)) {
            GeneralUtility::rmdir($extensionPath, true);
        }

        GeneralUtility::mkdir_deep($extensionPath);

        return $extensionPath . '/';
    }
}