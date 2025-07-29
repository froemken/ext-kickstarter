<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\AutoComplete;

class ExtensionKeyAutoComplete implements AutoCompleteInterface
{
    private const ARGUMENT_NAME = 'extension_key';

    public function getArgumentName(): string
    {
        return self::ARGUMENT_NAME;
    }

    public function __invoke(string $userInput): array
    {
        return ['TYPO3', 'WordPress', 'Plone'];
    }
}
