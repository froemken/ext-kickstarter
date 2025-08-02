<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Creator\SiteSet\SiteSetCreatorInterface;
use StefanFroemken\ExtKickstarter\Information\SiteSetInformation;

readonly class SiteSetCreatorService
{
    /**
     * @param iterable<SiteSetCreatorInterface> $siteSetCreators
     */
    public function __construct(
        private iterable $siteSetCreators,
    ) {}

    public function create(SiteSetInformation $siteSetInformation): void
    {
        foreach ($this->siteSetCreators as $creator) {
            $creator->create($siteSetInformation);
        }
    }
}
