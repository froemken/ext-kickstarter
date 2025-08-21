<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Service\Creator;

use FriendsOfTYPO3\Kickstarter\Information\EventInformation;
use FriendsOfTYPO3\Kickstarter\Information\Validation\InformationValidator;

readonly class EventCreatorService
{
    public function __construct(
        private iterable $eventCreators,
        private InformationValidator $infoValidator,
    ) {}

    public function create(EventInformation $eventInformation): void
    {
        $this->infoValidator->validate($eventInformation);

        foreach ($this->eventCreators as $creator) {
            $creator->create($eventInformation);
        }
    }
}
