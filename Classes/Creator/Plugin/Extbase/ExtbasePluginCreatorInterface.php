<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Plugin\Extbase;

use StefanFroemken\ExtKickstarter\Information\PluginInformation;

interface ExtbasePluginCreatorInterface
{
    public function create(PluginInformation $pluginInformation): void;
}
