<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Creator\SiteSet\SiteSettingsDefinitionCreatorInterface;
use StefanFroemken\ExtKickstarter\Information\SiteSettingsDefinitionInformation;

class SiteSettingsDefinitionCreatorService
{
    /**
     * @param iterable<SiteSettingsDefinitionCreatorInterface> $siteSettingDefinitionCreators
     */
    public function __construct(
        private iterable $siteSettingDefinitionCreators,
    ) {}

    public function create(SiteSettingsDefinitionInformation $siteSetInformation): void
    {
        foreach ($this->siteSettingDefinitionCreators as $creator) {
            $creator->create($siteSetInformation);
        }
    }
}
