<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.information.validator')]
class EventClassValidator implements ValidatorInterface
{
    private const POSTFIX = 'Event';

    public function __construct(
        private readonly ClassNameValidator $classNameValidator,
    ) {}

    public function __invoke(mixed $answer, InformationInterface $information, array $context = []): string
    {
        $answer = $this->classNameValidator->__invoke($answer, $information);
        if (!str_ends_with($answer, self::POSTFIX)) {
            throw new \RuntimeException(sprintf('Class name must end with "%s".', self::POSTFIX), 9245301485);
        }
        return $answer;
    }
}
