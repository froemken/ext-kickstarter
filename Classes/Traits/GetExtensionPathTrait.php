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

trait GetExtensionPathTrait
{
    private function getExtensionPath(string $extensionKey, bool $removePreviousExportDirectoryIfExists = false): string
    {
        if ($extensionKey === '') {
            throw new \InvalidArgumentException('Extension key must not be empty', 1741623620);
        }
        $extPath = sprintf(
            '%s/%s/%s',
            Environment::getPublicPath(),
            'typo3temp/ext-kickstarter',
            $extensionKey
        );

        if ($removePreviousExportDirectoryIfExists && is_dir($extPath)) {
            GeneralUtility::rmdir($extPath, true);
        }

        GeneralUtility::mkdir_deep($extPath);

        return $extPath . '/';
    }
}