<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\TypeConverterInformation;

readonly class TypeConverterCreatorService
{
    public function __construct(
        private iterable $typeConverterCreators,
    ) {}

    public function create(TypeConverterInformation $typeConverterInformation): void
    {
        foreach ($this->typeConverterCreators as $creator) {
            $creator->create($typeConverterInformation);
        }
    }
}
