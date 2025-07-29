<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input;

use StefanFroemken\ExtKickstarter\Command\Input\Validator\ValidatorInterface;

readonly class ValidatorFactory
{
    /**
     * @param iterable<ValidatorInterface> $validators
     */
    public function __construct(
        private iterable $validators,
    ) {}

    public function getValidator(string $property): ?ValidatorInterface
    {
        foreach ($this->validators as $validator) {
            if ($validator->getArgumentName() === $property) {
                return $validator;
            }
        }

        return null;
    }
}
