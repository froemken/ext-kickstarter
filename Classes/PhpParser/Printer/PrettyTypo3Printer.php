<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser\Printer;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\PrettyPrinter\Standard;

class PrettyTypo3Printer extends Standard
{
    /**
     * Add a new line before each comment
     */
    protected function pComments(array $comments): string
    {
        return $this->nl . parent::pComments($comments);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes): string
    {
        $multiline = false;
        $maxLineLength = 100;

        if ($nodes !== []) {
            if ($this->getLineLengthOfNodes($nodes) > $maxLineLength) {
                $multiline = true;
            }

            // For existing code, we should check, if arguments are already in multiline. Keep it.
            $startLine = reset($nodes)->getAttribute('startLine');
            $endLine = end($nodes)->getAttribute('endLine');
            if ($startLine !== $endLine) {
                $multiline = true;
            }
        }

        if ($multiline) {
            // We have to manually add trailing NL. Only leading NL will be done within this method. Don't know why.
            return $this->pCommaSeparatedMultiline($nodes, true) . $this->nl;
        }

        return $this->pImplode($nodes, ', ');
    }

    /**
     * Overwrites the original function to remove one space after 'declare('
     */
    protected function pStmt_Declare(Declare_ $node): string
    {
        return 'declare(' . $this->pCommaSeparated($node->declares) . ')'
            . (null !== $node->stmts ? ' {' . $this->pStmts($node->stmts) . $this->nl . '}' : ';');
    }

    protected function pStmt_ClassMethod(ClassMethod $node): string
    {
        return $this->pAttrGroups($node->attrGroups)
            . $this->pModifiers($node->flags)
            . 'function ' . ($node->byRef ? '&' : '') . $node->name
            . '(' . $this->pMaybeMultiline($node->params) . ')'
            . (null !== $node->returnType ? ': ' . $this->p($node->returnType) : '') // Removed extra space
            . (null !== $node->stmts
                ? $this->nl . '{' . $this->pStmts($node->stmts) . $this->nl . '}'
                : ';');
    }

    /**
     * Sum up all indent, method, argument and return type length to get full length of code.
     */
    private function getLineLengthOfNodes(array $nodes): int
    {
        // Play around with these settings
        $indentLength = 4;
        $methodNameLength = 25;
        $methodReturnTypeLength = 15;

        $lineLength = $indentLength + $methodNameLength + $methodReturnTypeLength;
        foreach ($nodes as $node) {
            if ($node instanceof Node\Arg) {
                // Adding 2 because of ", " between each argument
                $lineLength += strlen($this->p($node)) + 2;
            }
        }

        return $lineLength;
    }
}
