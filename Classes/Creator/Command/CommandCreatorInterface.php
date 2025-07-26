<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Command;

use StefanFroemken\ExtKickstarter\Information\CommandInformation;

interface CommandCreatorInterface
{
    public function create(CommandInformation $commandInformation): void;
}
