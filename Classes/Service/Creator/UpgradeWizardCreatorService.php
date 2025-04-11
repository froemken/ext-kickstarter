<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\UpgradeWizardInformation;

readonly class UpgradeWizardCreatorService
{
    public function __construct(
        private iterable $upgradeCreators,
    ) {}

    public function create(UpgradeWizardInformation $upgradeWizardInformation): void
    {
        foreach ($this->upgradeCreators as $creator) {
            $creator->create($upgradeWizardInformation);
        }
    }
}
