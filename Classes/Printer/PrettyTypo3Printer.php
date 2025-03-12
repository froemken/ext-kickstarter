<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Printer;

use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;

class PrettyTypo3Printer extends Standard
{
    /**
     * Move arguments of a method into next line.
     * Useful for ExtensionUtility::configurePlugin
     */
    protected function pCommaSeparated(array $nodes): string {
        if ($nodes !== [] && $nodes[0] instanceof Node\Arg) {
            return "\n    " . implode(",\n    ", array_map([$this, 'p'], $nodes)) . "\n";
        }

        return parent::pCommaSeparated($nodes);
    }
}
