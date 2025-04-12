<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser;

class StructureObjectStorage extends \SplObjectStorage
{
    /**
     * Param "sorted" is interesting for "traits" and "use" imports which should be inserted sorted
     */
    public function getStmts(bool $sorted = false): array
    {
        $stmts = [];

        foreach ($this as $structure) {
            if ($sorted && $name = $structure->getName()) {
                $stmts[$name] = $structure->getNode();
            } else {
                $stmts[] = $structure->getNode();
            }
        }

        ksort($stmts);

        // Because of array_push, we need increment array keys
        return array_values($stmts);
    }

    public function hasNodeWithName(string $name): bool
    {
        foreach ($this as $structure) {
            if ($structure->getName() === $name) {
                return true;
            }
        }

        return false;
    }
}
