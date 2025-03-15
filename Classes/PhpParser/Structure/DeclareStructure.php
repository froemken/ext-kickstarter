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
 * Contains the AST of a Declare_ node
 */
class DeclareStructure extends AbstractStructure
{
    private Node\Stmt\Declare_ $node;

    public function __construct(Node\Stmt\Declare_ $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node\Stmt\Declare_
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->declares[0]->key->toString();
    }
}
