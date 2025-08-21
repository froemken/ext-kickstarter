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

#[AutoconfigureTag('ext-kickstarter.inputHandler.middleware-identifier')]
class MiddlewareIdentifierNormalizer implements NormalizerInterface
{
    public function __invoke(?string $userInput): string
    {
        $corrected = strtolower($userInput??'');
        $corrected = preg_replace('#[^a-z0-9-/]+#', '-', $corrected);
        return trim($corrected, '-');
    }
}
