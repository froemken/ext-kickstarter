<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\ModelInformation;

readonly class ModelCreatorService
{
    public function __construct(
        private iterable $modelCreators,
    ) {}

    public function create(ModelInformation $modelInformation): void
    {
        foreach ($this->modelCreators as $creator) {
            $creator->create($modelInformation);
        }
    }
}
