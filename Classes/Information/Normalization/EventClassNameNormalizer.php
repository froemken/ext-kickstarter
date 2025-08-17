<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information\Normalization;

use FriendsOfTYPO3\Kickstarter\Command\Input\Normalizer\NormalizerInterface;
use FriendsOfTYPO3\Kickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.event-class')]
class EventClassNameNormalizer implements NormalizerInterface
{
    use TryToCorrectClassNameTrait;
    public function __invoke(?string $userInput): string
    {
        if ($userInput === null || $userInput === '') {
            return '';
        }

        return $this->tryToCorrectClassName($userInput, 'Event');
    }
}
