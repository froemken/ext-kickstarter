<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use StefanFroemken\ExtKickstarter\PhpParser\Structure;

/**
 * This visitor walks through all kind of detected PHP Parser nodes of any kind of PHP file
 * and transfers the nodes to FileStructure object, to have a grouped and sorted interpretation
 * of the analyzed file.
 */
final class FileVisitor extends NodeVisitorAbstract
{
    private Structure\FileStructure $fileStructure;

    public function __construct()
    {
        $this->fileStructure = new Structure\FileStructure();
    }

    /**
     * @return Node[]|null
     */
    public function beforeTraverse(array $nodes): ?array
    {
        return null;
    }

    /**
     * @return int|Node|Node[]|null
     */
    public function enterNode($node): int|Node|array|null
    {
        if ($node instanceof Node\Stmt\Declare_) {
            $this->fileStructure->addDeclareStructure(new Structure\DeclareStructure($node));
        } elseif ($node instanceof Node\Stmt\Namespace_) {
            $this->fileStructure->addNamespaceStructure(new Structure\NamespaceStructure($node));
        } elseif ($node instanceof Node\Stmt\Class_) {
            $this->fileStructure->addClassStructure(new Structure\ClassStructure($node));
        } elseif ($node instanceof Node\Stmt\TraitUse) {
            $this->fileStructure->addTraitStructure(new Structure\TraitStructure($node));
        } elseif ($node instanceof Node\Stmt\Use_) {
            $this->fileStructure->addUseStructure(new Structure\UseStructure($node));
        } elseif ($node instanceof Node\Stmt\ClassConst) {
            $this->fileStructure->addClassConstStructure(new Structure\ClassConstStructure($node));
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->fileStructure->addMethodStructure(new Structure\MethodStructure($node));
        } elseif ($node instanceof Node\Stmt\Property) {
            $this->fileStructure->addPropertyStructure(new Structure\PropertyStructure($node));
        } elseif ($node instanceof Node\Stmt\Function_) {
            $this->fileStructure->addFunctionStructure(new Structure\FunctionStructure($node));
        } elseif ($node instanceof Node\Stmt\Expression) {
            $this->fileStructure->addExpressionStructure(new Structure\ExpressionStructure($node));
        } elseif ($node instanceof Node\Stmt\Return_) {
            $this->fileStructure->addReturnStructure(new Structure\ReturnStructure($node));
        }

        return null;
    }

    public function getFileStructure(): Structure\FileStructure
    {
        return $this->fileStructure;
    }
}
