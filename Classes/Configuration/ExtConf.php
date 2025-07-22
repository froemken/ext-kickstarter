<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Configuration;

use StefanFroemken\ExtKickstarter\Model\Dto\Settings;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;

/**
 * This class will streamline the values from extension manager configuration
 */
#[Autoconfigure(constructor: 'create')]
final class ExtConf
{
    private const EXT_KEY = 'ext_kickstarter';

    public function __construct(
        // general
        private string $exportDirectory = Settings::DEFAULT_SETTINGS['exportDirectory'],
        private bool $activateModule = Settings::DEFAULT_SETTINGS['activateModule'],
    ) {}

    public static function create(ExtensionConfiguration $extensionConfiguration): self
    {
        $extensionSettings = Settings::getSettings($extensionConfiguration);

        return new self(
            // general
            exportDirectory: $extensionSettings->exportDirectory,
            activateModule: $extensionSettings->activateModule,
        );
    }

    public function getExportDirectory(): string
    {
        $exportDirectory = trim($this->exportDirectory);

        if (!$exportDirectory) {
            // Fall back to typo3temp/ext-kickstarter
            return sprintf(
                '/%s/%s/',
                trim(Environment::getPublicPath(), '/'),
                'typo3temp/ext-kickstarter',
            );
        }

        // sprintf() in ExtensionInformation will add trailing slash
        return Environment::getProjectPath() . '/' . rtrim($exportDirectory, '/');
    }

    public function isActivateModule(): bool
    {
        return $this->activateModule;
    }
}
