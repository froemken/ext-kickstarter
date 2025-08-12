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
 * Contains the AST of a ClassConst node
 */
class ClassConstStructure extends AbstractStructure
{
    private Node\Stmt\ClassConst $node;

    public function __construct(Node\Stmt\ClassConst $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node\Stmt\ClassConst
    {
        return $this->node;
    }

    public function getName(): string
    {
        return $this->node->consts[0]->name->toString();
    }
}
