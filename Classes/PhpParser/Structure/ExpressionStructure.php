<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser\Structure;

use PhpParser\Node\Stmt\Expression;

/**
 * Contains the AST of a StaticCall node
 */
class ExpressionStructure extends AbstractStructure
{
    private Expression $node;

    public function __construct(Expression $node)
    {
        $this->node = $node;
    }

    public function getNode(): Expression
    {
        return $this->node;
    }

    public function getName(): string
    {
        return property_exists($this->node->expr, 'name')
            ? $this->node->expr->name->toString()
            : '';
    }
}
