<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Context\CommandContext;

interface QuestionInterface
{
    public function getArgumentName(): string;

    public function ask(CommandContext $commandContext, ?string $default = null): mixed;
}
