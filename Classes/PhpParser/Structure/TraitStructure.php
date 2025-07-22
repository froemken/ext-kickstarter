<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser\Structure;

use PhpParser\Node\Stmt\TraitUse;

/**
 * Contains the AST of a TraitUse node
 */
class TraitStructure extends AbstractStructure
{
    private TraitUse $node;

    public function __construct(TraitUse $node)
    {
        $this->node = $node;
    }

    public function getNode(): TraitUse
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->traits[0]->toString();
    }
}
