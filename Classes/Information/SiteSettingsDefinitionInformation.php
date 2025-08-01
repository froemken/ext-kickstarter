<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

use TYPO3\CMS\Core\Settings\CategoryDefinition;
use TYPO3\CMS\Core\Settings\SettingDefinition;

readonly class SiteSettingsDefinitionInformation
{
    private const CONFIG_FILENAME = 'settings.definitions.yaml';

    /**
     * @param array<CategoryDefinition> $categories
     * @param array<SettingDefinition> $settings
     */
    public function __construct(
        private ExtensionInformation $extensionInformation,
        private SiteSetInformation $siteSetInformation,
        private array $categories,
        private array $settings,
        private CreatorInformation $creatorInformation = new CreatorInformation(),
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getSiteSetFilePath(): string
    {
        return $this->siteSetInformation->getSiteSetPath() . self::CONFIG_FILENAME;
    }

    public function getSiteSetPath(): string
    {
        return $this->siteSetInformation->getSiteSetPath();
    }

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }

    public function getSiteSetInformation(): SiteSetInformation
    {
        return $this->siteSetInformation;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }
}
