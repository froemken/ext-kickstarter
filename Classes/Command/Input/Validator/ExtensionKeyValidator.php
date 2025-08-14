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

#[AutoconfigureTag('ext-kickstarter.inputHandler.extension_key')]
class ExtensionKeyValidator implements ValidatorInterface
{
    public function __invoke(mixed $answer): string
    {
        $length = mb_strlen($answer);

        if ($length < 3 || $length > 30) {
            throw new \RuntimeException(
                'Extension key length must be between 3 and 30 characters',
                1753821969,
            );
        }
        if (in_array(preg_match('/^[a-z][a-z0-9_]*$/', $answer), [0, false], true)) {
            throw new \RuntimeException(
                'Extension key can only start with a lowercase letter and contain lowercase letters, numbers, or underscores',
                1753821971,
            );
        }
        if (preg_match('/^[_]|[_]$/', $answer)) {
            throw new \RuntimeException(
                'Extension key cannot start or end with an underscore',
                1753821973,
            );
        }
        if (preg_match('/^(tx|user_|pages|tt_|sys_|ts_language|csh_)/', $answer)) {
            throw new \RuntimeException(
                'Extension key cannot start with reserved prefixes such as tx, user_, pages, tt_, sys_, ts_language, or csh_',
                1753821975,
            );
        }

        return $answer;
    }
}
