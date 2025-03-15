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
 * Contains the AST of a StaticCall node
 */
class ExpressionStructure extends AbstractStructure
{
    private Node\Stmt\Expression $node;

    public function __construct(Node\Stmt\Expression $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node\Stmt\Expression
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->expr->name->toString();
    }
}
