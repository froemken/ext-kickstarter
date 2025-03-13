<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Controller;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Name;
use StefanFroemken\ExtKickstarter\Information\ControllerInformation;
use StefanFroemken\ExtKickstarter\Printer\PrettyTypo3Printer;
use StefanFroemken\ExtKickstarter\Traits\ExtensionPathTrait;
use StefanFroemken\ExtKickstarter\Traits\PhpParserStatementTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtbaseControllerCreator
{
    use ExtensionPathTrait;
    use PhpParserStatementTrait;

    private PrettyTypo3Printer $prettyTypo3Printer;

    public function __construct(
        PrettyTypo3Printer $prettyTypo3Printer,
    ) {
        $this->prettyTypo3Printer = $prettyTypo3Printer;
    }

    public function create(ControllerInformation $controllerInformation): void
    {
        $builderFactory = new BuilderFactory();
        $extensionPath = $this->getExtensionPath($controllerInformation->getExtensionKey());
        $controllerPath = $extensionPath . 'Classes/Controller/';
        GeneralUtility::mkdir_deep($controllerPath);

        $targetFile = $controllerPath . $controllerInformation->getControllerFilename();

        if (is_file($targetFile)) {
            file_put_contents(
                $targetFile,
                $this->prettyTypo3Printer->prettyPrint(
                    $this->getAstForController($controllerInformation, $builderFactory),
                ),
                FILE_APPEND,
            );
        } else {
            file_put_contents(
                $targetFile,
                $this->prettyTypo3Printer->prettyPrintFile($this->getAstForController()),
            );
        }
    }

    private function getAstForController(): array
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
                    new Node\Stmt\UseUse(new Node\Name('TYPO3\CMS\Extbase\Mvc\Controller\ActionController')),
                ]),
                new Node\Stmt\Class_(new Name('BlogExampleController'), [
                    'extends' => new Node\Name('ActionController'),
                ]),
            ]
        );

        return [
            $declareStrictNode,
            $namespaceNode,
            new Node\Stmt\Nop(),
        ];
    }
}
