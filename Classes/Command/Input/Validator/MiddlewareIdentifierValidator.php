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

#[AutoconfigureTag('ext-kickstarter.inputHandler.middleware-identifier')]
class MiddlewareIdentifierValidator implements ValidatorInterface
{
    public function __invoke(mixed $answer): string
    {
        // Simple check for empty input
        if (($answer??'') === '') {
            throw new \RuntimeException('Middleware identifier cannot be empty.', 6775210887);
        }

        if (preg_match('/^\d/', $answer)) {
            throw new \RuntimeException('Middleware identifier must not start with a number.', 9046337622);
        }
        if (in_array(preg_match('#[a-z][a-z0-9-]+/[a-z][a-z0-9-]+#', $answer), [0, false], true)) {
            throw new \RuntimeException('Middleware identifier should have format prefix/identifier and only use alphanumerics.', 1020200855);
        }

        return $answer;
    }
}
