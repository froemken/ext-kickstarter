<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\SitePackageInformation;

readonly class SitePackageCreatorService
{
    public function __construct(
        private iterable $sitePackageCreators,
    ) {}

    public function create(SitePackageInformation $sitePackageInformation): void
    {
        foreach ($this->sitePackageCreators as $creator) {
            $creator->create($sitePackageInformation);
        }
    }
}
