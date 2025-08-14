<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input\AutoComplete;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.extension_key')]
class ExtensionKeyAutoComplete implements AutoCompleteInterface
{
    public function __invoke(string $userInput): array
    {
        return ['sitepackage', 'my_extension', 'demo'];
    }
}
