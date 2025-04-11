<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\EventInformation;

readonly class EventCreatorService
{
    public function __construct(
        private iterable $eventCreators,
    ) {}

    public function create(EventInformation $eventInformation): void
    {
        foreach ($this->eventCreators as $creator) {
            $creator->create($eventInformation);
        }
    }
}
