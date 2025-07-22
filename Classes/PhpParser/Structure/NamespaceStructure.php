<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser\Structure;

use PhpParser\Node\Stmt\Namespace_;

/**
 * Contains the AST of a Namespace_ node
 */
class NamespaceStructure extends AbstractStructure
{
    private Namespace_ $node;

    public function __construct(Namespace_ $node)
    {
        $this->node = $node;
    }

    public function getNode(): Namespace_
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->name->toString();
    }
}
