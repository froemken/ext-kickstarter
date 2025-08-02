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
use StefanFroemken\ExtKickstarter\Information\SiteSetInformation;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteSetCreator implements SiteSetCreatorInterface
{
    use FileStructureBuilderTrait;

    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(SiteSetInformation $siteSetInformation): void
    {
        GeneralUtility::mkdir_deep($siteSetInformation->getSiteSetPath());
        if (file_exists($siteSetInformation->getSiteSetFilePath())) {
            $siteSetInformation->getCreatorInformation()->fileModificationFailed(
                $siteSetInformation->getSiteSetFilePath(),
                sprintf('The site set %s can not be created, there is already a site set at path %s', $siteSetInformation->getIdentifier(), $siteSetInformation->getPath())
            );
            return;
        }
        $this->fileManager->createFile(
            $siteSetInformation->getSiteSetFilePath(),
            $this->getFileContent($siteSetInformation),
            $siteSetInformation->getCreatorInformation()
        );
    }

    private function getFileContent(SiteSetInformation $siteSetInformation): string
    {
        $siteSetConfig = [
            'name' => $siteSetInformation->getIdentifier(),
            'label' => $siteSetInformation->getLabel(),
        ];
        if ($siteSetInformation->getDependencies() !== []) {
            $siteSetConfig['dependencies'] = $siteSetInformation->getDependencies();
        }
        if ($siteSetInformation->isHidden()) {
            $siteSetConfig['hidden'] = true;
        }

        return Yaml::dump($siteSetConfig, 2, 2);
    }
}
