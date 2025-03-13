<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Controller;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard;
use StefanFroemken\ExtKickstarter\Information\ControllerInformation;
use StefanFroemken\ExtKickstarter\Printer\PrettyTypo3Printer;
use StefanFroemken\ExtKickstarter\Traits\ExtensionPathTrait;
use StefanFroemken\ExtKickstarter\Traits\PhpParserStatementTrait;
use StefanFroemken\ExtKickstarter\Visitor\ControllerVisitor;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ControllerCreator
{
    use ExtensionPathTrait;
    use PhpParserStatementTrait;

    public function create(ControllerInformation $controllerInformation): void
    {
        $extensionPath = $this->getExtensionPath($controllerInformation->getExtensionKey());
        $controllerPath = $extensionPath . 'Classes/Controller/';
        GeneralUtility::mkdir_deep($controllerPath);

        $targetFile = $controllerPath . $controllerInformation->getControllerFilename();
        $standardPrinter = new Standard(['shortArraySyntax' => true]);

        $actionMethodNameStmts = [];
        if (is_file($targetFile)) {
            $stmts = $this->getParserStatementsForFile($targetFile);
            foreach ($controllerInformation->getActionMethodNames() as $actionMethodName) {
                if (!$this->hasActionMethodName($actionMethodName, $stmts)) {
                    $actionMethodNameStmts[] = $this->getStmtForActionMethodName($actionMethodName);
                }
            }

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new ControllerVisitor($actionMethodNameStmts));
            $stmts = $traverser->traverse($stmts);

            file_put_contents(
                $targetFile,
                $standardPrinter->prettyPrintFile($stmts),
            );
        } else {
            foreach ($controllerInformation->getActionMethodNames() as $actionMethodName) {
                $actionMethodNameStmts[] = $this->getStmtForActionMethodName($actionMethodName);
            }
            file_put_contents(
                $targetFile,
                $standardPrinter->prettyPrintFile($this->getStmtsForController($actionMethodNameStmts)),
            );
        }
    }

    private function getStmtsForController(array $actionMethodNameStmts): array
    {
        $declareStrictNode = new Node\Stmt\Declare_([
            new Node\Stmt\DeclareDeclare('strict_types', new Node\Scalar\LNumber(1))
        ]);

        $namespaceNode = new Node\Stmt\Namespace_(
            new Node\Name('StefanFroemken\BlogExample\Controller'),
            [
                new Node\Stmt\Use_([
                    new Node\Stmt\UseUse(new Node\Name('Psr\Http\Message\ResponseInterface')),
                ]),
                new Node\Stmt\Use_([
                    new Node\Stmt\UseUse(new Node\Name('TYPO3\CMS\Core\Http\HtmlResponse')),
                ]),
                new Node\Stmt\Class_(new Name('BlogExampleController'), [
                    'stmts' => $actionMethodNameStmts,
                ]),
            ]
        );

        return [
            $declareStrictNode,
            $namespaceNode,
            new Node\Stmt\Nop(),
        ];
    }

    private function getStmtForActionMethodName(string $actionMethodName): Node
    {
        return new Node\Stmt\ClassMethod(new Node\Identifier($actionMethodName), [
            'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
            'returnType' => new Node\Name('ResponseInterface'),
            'stmts' => [
                new Node\Stmt\Return_(
                    new Node\Expr\New_(
                        new Node\Name('HtmlResponse'),
                        [
                            new Node\Arg(new Node\Scalar\String_('Hello World!'))
                        ]
                    )
                ),
            ],
        ]);
    }

    private function hasActionMethodName(string $actionMethodName, array $stmts): bool
    {
        $nodeFinder = new NodeFinder();

        $matchingStatements = $nodeFinder->find($stmts, function(Node $node) use ($actionMethodName) {
            return $node instanceof Node\Stmt\ClassMethod && $node->name->toString() === $actionMethodName;
        });

        return $matchingStatements !== [];
    }
}
