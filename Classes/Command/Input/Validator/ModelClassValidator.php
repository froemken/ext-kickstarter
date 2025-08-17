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

#[AutoconfigureTag('ext-kickstarter.inputHandler.model-class')]
class ModelClassValidator implements ValidatorInterface
{
    public function __construct(
        private readonly ClassNameValidator $classNameValidator,
    ) {}

    public function __invoke(mixed $answer): string
    {
        return $this->classNameValidator->__invoke($answer);
    }
}
