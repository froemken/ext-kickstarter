<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\PhpParser\Structure;

use PhpParser\Node;

/**
 * Contains the AST of a Use_ node
 */
class UseStructure extends AbstractStructure
{
    private Node\Stmt\Use_ $node;

    public function __construct(Node\Stmt\Use_ $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node\Stmt\Use_
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->uses[0]->name->toString();
    }
}
