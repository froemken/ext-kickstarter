<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Visitor;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitorAbstract;

final class ControllerVisitor extends NodeVisitorAbstract
{
    private array $actionMethodNameStmts;

    public function __construct(array $actionMethodNameStmts)
    {
        $this->actionMethodNameStmts = $actionMethodNameStmts;
    }

    /**
     * Add use import for ResponseInterface, if missing
     */
    public function beforeTraverse(array $nodes): ?array
    {
        $nodeFinder = new NodeFinder();

        // Check, if ResponseInterface exists in use imports
        $existingUseNodes = $nodeFinder->find($nodes, function (Node $node) {
            return $node instanceof Node\Stmt\Use_;
        });

        if (!$existingUseNodes) {
            $newUseImport = new Node\Stmt\Use_([
                new Node\Stmt\UseUse(new Node\Name('Psr\Http\Message\ResponseInterface'))
            ]);

            // Suche die richtige Position für das Einfügen
            $lastUseNodeIndex = null;
            foreach ($nodes as $index => $node) {
                if ($node instanceof Node\Stmt\Use_) {
                    $lastUseNodeIndex = $index;
                }
            }

            // Insert the new use-import after the last use-statement
            if ($lastUseNodeIndex !== null) {
                array_splice($nodes, $lastUseNodeIndex + 1, 0, [$newUseImport]);
            } else {
                // Fallback: Es gibt keine anderen use-Statements, also an den Anfang einfügen
                array_unshift($nodes, $newUseImport);
            }
        }

        return $nodes;
    }

    /**
     * Add additional controller action methods
     */
    public function leaveNode(Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\Class_) {
            array_push($node->stmts, ...$this->actionMethodNameStmts);
            return $node;
        }

        return null;
    }
}
