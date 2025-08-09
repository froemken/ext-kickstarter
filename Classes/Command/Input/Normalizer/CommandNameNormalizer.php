<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Normalizer;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.command-name')]
class CommandNameNormalizer implements NormalizerInterface
{
    public function __invoke(?string $userInput): string
    {
        // Remove invalid chars
        $cleanedCommandName = preg_replace('/[^a-zA-Z0-9:]/', '', $userInput??'');

        return $cleanedCommandName;
    }
}
