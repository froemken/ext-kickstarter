<?php

namespace StefanFroemken\ExtKickstarter\Model\Dto;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class Settings
{
    private const EXT_KEY = 'ext_kickstarter';

    public const DEFAULT_SETTINGS = [
        // general
        'exportDirectory' => '',
        'preferredExtension' => '',
        'activateModule' => false,
    ];

    public function __construct(
        public readonly string $exportDirectory = self::DEFAULT_SETTINGS['exportDirectory'],
        public readonly string $preferredExtension = self::DEFAULT_SETTINGS['preferredExtension'],
        public readonly bool $activateModule = self::DEFAULT_SETTINGS['activateModule'],
    ) {}

    public static function getSettings(ExtensionConfiguration $extensionConfiguration): Settings
    {
        $extensionSettings = Settings::DEFAULT_SETTINGS;

        // Overwrite default extension settings with values from EXT_CONF
        try {
            $extensionSettings = array_merge(
                $extensionSettings,
                $extensionConfiguration->get(self::EXT_KEY),
            );
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
        }
        return new Settings($extensionSettings['exportDirectory'], $extensionSettings['preferredExtension'], $extensionSettings['activateModule']);
    }
}
