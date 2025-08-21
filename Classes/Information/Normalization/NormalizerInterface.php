<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information\Normalization;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;

interface NormalizerInterface
{
    public function __invoke(?string $userInput, InformationInterface $information): string;
}
