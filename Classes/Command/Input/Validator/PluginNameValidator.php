<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Validator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.plugin-name')]
class PluginNameValidator implements ValidatorInterface
{
    public function __invoke(mixed $answer): string
    {
        // Simple check for empty input
        if (($answer ?? '') === '') {
            throw new \RuntimeException('Plugin name cannot be empty.', 5117711087);
        }

        if (preg_match('/^\d/', $answer)) {
            throw new \RuntimeException('Plugin name must not start with a number.', 2198581353);
        }
        if (in_array(preg_match('#[A-Z][a-zA-Z0-9]+#', $answer), [0, false], true)) {
            throw new \RuntimeException('Plugin name should have format prefix/identifier and only use alphanumerics.', 3030957806);
        }

        return $answer;
    }
}
