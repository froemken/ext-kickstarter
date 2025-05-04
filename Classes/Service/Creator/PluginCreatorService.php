<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service\Creator;

use StefanFroemken\ExtKickstarter\Information\PluginInformation;

class PluginCreatorService
{
    public function __construct(
        private readonly iterable $extbasePluginCreators,
        private readonly iterable $nativePluginCreators,
    ) {}

    public function create(PluginInformation $pluginInformation): void
    {
        if ($pluginInformation->isExtbasePlugin()) {
            foreach ($this->extbasePluginCreators as $creator) {
                $creator->create($pluginInformation);
            }
        } else {
            foreach ($this->nativePluginCreators as $creator) {
                $creator->create($pluginInformation);
            }
        }
    }
}
