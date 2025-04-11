<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\EventListenerInformation;

readonly class EventListenerCreatorService
{
    public function __construct(
        private iterable $eventListenerCreators,
    ) {}

    public function create(EventListenerInformation $eventListenerInformation): void
    {
        foreach ($this->eventListenerCreators as $creator) {
            $creator->create($eventListenerInformation);
        }
    }
}
