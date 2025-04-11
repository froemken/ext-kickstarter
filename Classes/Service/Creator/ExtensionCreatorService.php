<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;

readonly class ExtensionCreatorService
{
    public function __construct(
        private iterable $extensionCreators,
    ) {}

    public function create(ExtensionInformation $extensionInformation): void
    {
        foreach ($this->extensionCreators as $creator) {
            $creator->create($extensionInformation);
        }
    }
}
