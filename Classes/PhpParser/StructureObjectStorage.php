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
    public function getStmts(): array
    {
        $stmts = [];

        foreach ($this as $structure) {
            $stmts[] = $structure->getNode();
        }

        return $stmts;
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
