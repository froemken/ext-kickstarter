<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\CommandInformation;

readonly class CommandCreatorService
{
    public function __construct(
        private iterable $commandCreators,
    ) {}

    public function create(CommandInformation $commandInformation): void
    {
        foreach ($this->commandCreators as $creator) {
            $creator->create($commandInformation);
        }
    }
}
