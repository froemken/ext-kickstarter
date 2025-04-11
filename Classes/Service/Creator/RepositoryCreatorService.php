<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\RepositoryInformation;

readonly class RepositoryCreatorService
{
    public function __construct(
        private iterable $repositoryCreators,
    ) {}

    public function create(RepositoryInformation $repositoryInformation): void
    {
        foreach ($this->repositoryCreators as $creator) {
            $creator->create($repositoryInformation);
        }
    }
}
