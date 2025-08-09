<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Validator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.command-name')]
class CommandNameValidator implements ValidatorInterface
{
    public function __invoke(mixed $answer): string
    {
        if ($answer === null || $answer === '') {
            throw new \RuntimeException('Command name must not be empty.', 7735000785);
        }
        if (preg_match('/^\d/', $answer)) {
            throw new \RuntimeException('Command name must not start with a number.', 3827645034);
        }
        if (preg_match('/[^a-zA-Z0-9:]/', $answer)) {
            throw new \RuntimeException('Command name contains invalid chars. Please provide just letters, numbers and colon (:).', 8283531582);
        }
        return $answer;
    }
}
