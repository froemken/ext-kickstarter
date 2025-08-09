<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Normalizer;

use StefanFroemken\ExtKickstarter\Traits\TryToCorrectMethodNameTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.controller-action')]
class ActionMethodNameNormalizer implements NormalizerInterface
{
    use TryToCorrectMethodNameTrait;
    public function __invoke(?string $userInput): string
    {
        if ($userInput === null || $userInput === '') {
            return '';
        }

        return $this->tryToCorrectMethodName($userInput, 'Action');
    }
}
