<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Normalizer;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.extension_key')]
class ExtensionKeyNormalizer implements NormalizerInterface
{
    public function __invoke(?string $userInput): string
    {
        if ($userInput === null || $userInput === '') {
            return '';
        }
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
