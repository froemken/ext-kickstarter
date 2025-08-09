<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Normalizer;

use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.command-class')]
class CommandClassNameNormalizer implements NormalizerInterface
{
    use TryToCorrectClassNameTrait;
    private const POSTFIX = 'Command';
    public function __invoke(?string $userInput): string
    {
        if ($userInput === null || $userInput === '') {
            return '';
        }

        return $this->tryToCorrectClassName($userInput, self::POSTFIX);
    }
}
