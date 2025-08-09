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

#[AutoconfigureTag('ext-kickstarter.inputHandler.command-class')]
class CommandClassValidator implements ValidatorInterface
{
    private const POSTFIX = 'Command';

    public function __construct(
        private readonly ClassNameValidator $classNameValidator,
    ) {
    }
    public function __invoke(mixed $answer): string
    {
        $answer = $this->classNameValidator->__invoke($answer);
        if (!str_ends_with($answer, self::POSTFIX)) {
            throw new \RuntimeException(sprintf('Class name must end with "%s".', self::POSTFIX), 9245301485);
        }
        return $answer;
    }
}
