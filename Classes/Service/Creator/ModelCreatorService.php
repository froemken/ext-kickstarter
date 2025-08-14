<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Service\Creator;

use FriendsOfTYPO3\Kickstarter\Information\ModelInformation;

class ModelCreatorService
{
    public function __construct(
        private readonly iterable $modelCreators,
    ) {}

    public function create(ModelInformation $modelInformation): void
    {
        foreach ($this->modelCreators as $creator) {
            $creator->create($modelInformation);
        }
    }
}
