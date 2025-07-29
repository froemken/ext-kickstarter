<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input;

use StefanFroemken\ExtKickstarter\Command\Input\AutoComplete\AutoCompleteInterface;

readonly class AutoCompleteFactory
{
    /**
     * @param iterable<AutoCompleteInterface> $autoCompletes
     */
    public function __construct(
        private iterable $autoCompletes,
    ) {}

    public function getAutoComplete(
        string $propertyName,
    ): ?AutoCompleteInterface {
        foreach ($this->autoCompletes as $autoComplete) {
            if ($autoComplete->getArgumentName() === $propertyName) {
                return $autoComplete;
            }
        }

        return null;
    }
}
