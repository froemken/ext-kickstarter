<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

use StefanFroemken\ExtKickstarter\Configuration\ExtConf;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait ExtensionInformationTrait
{
    /**
     * Collects data from composer.json to build ExtensionInformation object.
     * Can only be called for existing extensions with existing composer.json file
     */
    private function getExtensionInformation(string $extensionKey, SymfonyStyle $io): ExtensionInformation
    {
        $extensionPath = $this->getExtensionPath($extensionKey);
        $composerManifestPath = $extensionPath . 'composer.json';

        if (!is_dir($extensionPath)) {
            $io->error([
                'Extension path does not exists: ' . $extensionPath,
                'Please use command "make:extension" to create a new extension.',
            ]);
            die();
        }

        if (!is_file($composerManifestPath)) {
            $io->error([
                'Extension "' . $extensionKey . '" does not have a composer.json file.',
                'Seems that the existing directory is no TYPO3 extension.',
                'Please use command "make:extension" to create a new extension.',
            ]);
            die();
        }

        try {
            $composerManifest = json_decode((file_get_contents($composerManifestPath) ?: ''), true, 512, JSON_THROW_ON_ERROR) ?? [];
        } catch (\JsonException $e) {
            $io->error(['Could not decode composer.json. Please check syntax: ' . $e->getMessage()]);
        }

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

        // We are in a trait. I would try to prevent moving it into inject or constructor
        // You will never know, from where this trait will be called ;-)
        $extConf = GeneralUtility::makeInstance(ExtConf::class);

        return sprintf(
            '%s/%s/',
            $extConf->getExportDirectory(),
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
