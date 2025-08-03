<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\SiteSet;

use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\SiteSettingsDefinitionInformation;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteSettingsDefinitionCreator implements SiteSettingsDefinitionCreatorInterface
{
    use FileStructureBuilderTrait;

    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(SiteSettingsDefinitionInformation $siteSettingsDefinitionInformation): void
    {
        GeneralUtility::mkdir_deep($siteSettingsDefinitionInformation->getSiteSetPath());
        if (file_exists($siteSettingsDefinitionInformation->getSiteSetFilePath())) {
            $siteSettingsDefinitionInformation->getCreatorInformation()->fileModificationFailed(
                $siteSettingsDefinitionInformation->getSiteSetFilePath(),
                sprintf(
                    'The site settings definition can not be created, there is already a settings definition at path %s',
                    $siteSettingsDefinitionInformation->getSiteSetInformation()->getPath()
                )
            );
            return;
        }
        $this->fileManager->createFile(
            $siteSettingsDefinitionInformation->getSiteSetFilePath(),
            $this->getFileContent($siteSettingsDefinitionInformation),
            $siteSettingsDefinitionInformation->getCreatorInformation()
        );
    }

    private function getFileContent(SiteSettingsDefinitionInformation $siteSettingsDefinitionInformation): string
    {
        $siteSettingsDefinitionConfig = [];
        if ($siteSettingsDefinitionInformation->getCategories() !== []) {
            $siteSettingsDefinitionConfig['categories'] = [];
            foreach ($siteSettingsDefinitionInformation->getCategories() as $category) {
                $array = $category->toArray();

                unset($array['key']);

                $siteSettingsDefinitionConfig['categories'][$category->key] = $array;
            }
        }
        foreach ($siteSettingsDefinitionInformation->getSettings() as $setting) {
            $array = $setting->toArray();

            unset($array['key']);
            if ($array['readonly'] === false) {
                unset($array['readonly']);
            }

            // Ensure ordering: label -> type -> default -> (rest)
            $ordered = [];
            foreach (['label', 'type', 'default'] as $key) {
                if (array_key_exists($key, $array)) {
                    $ordered[$key] = $array[$key];
                    unset($array[$key]);
                }
            }
            // Append remaining keys in their original order
            foreach ($array as $k => $v) {
                $ordered[$k] = $v;
            }

            $siteSettingsDefinitionConfig['settings'][$setting->key] = $ordered;
        }

        return Yaml::dump($siteSettingsDefinitionConfig, 4, 2);
    }
}
