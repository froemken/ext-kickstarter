<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Normalizer;

class ExtensionKeyNormalizer implements NormalizerInterface
{
    private const ARGUMENT_NAME = 'extension_key';

    public function getArgumentName(): string
    {
        return self::ARGUMENT_NAME;
    }

    public function __invoke(string $userInput): string
    {
        // Lower case the given extension key
        $cleanedUserInput = strtolower($userInput);

        // Change "-" to "_". Migrates package names to the extension key
        $cleanedUserInput = str_replace('-', '_', $cleanedUserInput);

        // Remove invalid chars
        $cleanedUserInput = preg_replace('/[^a-z0-9_]/', '', $cleanedUserInput);

        // Remove leading numbers
        $cleanedUserInput = preg_replace('/^\d+/', '', $cleanedUserInput);

        // Remove leading and trailing "_"
        return trim($cleanedUserInput, '_');
    }
}
