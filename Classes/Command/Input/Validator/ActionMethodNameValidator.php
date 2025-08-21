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

#[AutoconfigureTag('ext-kickstarter.inputHandler.controller-action')]
class ActionMethodNameValidator implements ValidatorInterface
{
    public function __construct(
        private readonly MethodNameValidator $methodNameValidator,
    ) {
    }
    public function __invoke(mixed $answer): string
    {
        $answer = $this->methodNameValidator->__invoke($answer);
        if (!str_ends_with($answer, 'Action')) {
            throw new \RuntimeException('Method name must end with "Action".', 2791217025);
        }
        return $answer;
    }
}
