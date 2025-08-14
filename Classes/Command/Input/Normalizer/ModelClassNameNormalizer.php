<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Normalizer;

use FriendsOfTYPO3\Kickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.model-class')]
class ModelClassNameNormalizer implements NormalizerInterface
{
    use TryToCorrectClassNameTrait;
    public function __invoke(?string $userInput): string
    {
        if ($userInput === null || $userInput === '') {
            return '';
        }
        if(str_contains($userInput, '/')){
            $userInput = substr($userInput, strpos($userInput, '/') + 1);
        }

        return $this->tryToCorrectClassName($userInput);
    }
}
