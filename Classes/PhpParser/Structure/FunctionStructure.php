<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser\Structure;

use PhpParser\Node;

/**
 * Contains the AST of a Function_ node
 */
class FunctionStructure extends AbstractStructure
{
    private Node\Stmt\Function_ $node;

    public function __construct(Node\Stmt\Function_ $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node\Stmt\Function_
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->name->toString();
    }
}
