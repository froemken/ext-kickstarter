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
 * Contains the AST of a ClassMethod node
 */
class MethodStructure extends AbstractStructure
{
    private Node\Stmt\ClassMethod $node;

    public function __construct(Node\Stmt\ClassMethod $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node\Stmt\ClassMethod
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->name->toString();
    }
}
