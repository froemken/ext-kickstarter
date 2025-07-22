<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser\Structure;

use PhpParser\Node\Stmt\Use_;

/**
 * Contains the AST of a Use_ node
 */
class UseStructure extends AbstractStructure
{
    private Use_ $node;

    public function __construct(Use_ $node)
    {
        $this->node = $node;
    }

    public function getNode(): Use_
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->uses[0]->name->toString();
    }
}
