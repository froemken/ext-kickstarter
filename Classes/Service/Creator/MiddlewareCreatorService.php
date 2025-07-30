<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Creator\Middleware\MiddlewareCreatorInterface;
use StefanFroemken\ExtKickstarter\Information\MiddleWareInformation;

readonly class MiddlewareCreatorService
{
    /**
     * @param iterable<MiddlewareCreatorInterface> $middlewareCreators
     */
    public function __construct(
        private iterable $middlewareCreators,
    ) {}

    public function create(MiddlewareInformation $middlewareInformation): void
    {
        /** @var MiddlewareCreatorInterface $creator */
        foreach ($this->middlewareCreators as $creator) {
            $creator->create($middlewareInformation);
        }
    }
}
