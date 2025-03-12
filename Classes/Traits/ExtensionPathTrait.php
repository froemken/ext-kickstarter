<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait ExtensionPathTrait
{
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