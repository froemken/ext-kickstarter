<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\TestEnvInformation;

class TestEnvCreatorService
{
    public function __construct(
        private readonly iterable $testEnvCreators,
    ) {}

    public function create(TestEnvInformation $testEnvInformation): void
    {
        foreach ($this->testEnvCreators as $creator) {
            $creator->create($testEnvInformation);
        }
    }
}
