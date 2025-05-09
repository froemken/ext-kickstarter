<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\ControllerInformation;

readonly class ControllerCreatorService
{
    public function __construct(
        private iterable $extbaseControllerCreators,
        private iterable $nativeControllerCreators,
    ) {}

    public function create(ControllerInformation $controllerInformation): void
    {
        if ($controllerInformation->isExtbaseController()) {
            foreach ($this->extbaseControllerCreators as $creator) {
                $creator->create($controllerInformation);
            }
        } else {
            foreach ($this->nativeControllerCreators as $creator) {
                $creator->create($controllerInformation);
            }
        }
    }
}
