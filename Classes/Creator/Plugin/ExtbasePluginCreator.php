<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Plugin;

use Nette\PhpGenerator\Dumper;
use PhpParser\BuilderFactory;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\Traits\ExtensionPathTrait;
use StefanFroemken\ExtKickstarter\Visitor\ExtLocalconfVisitor;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class ExtbasePluginCreator
{
    use ExtensionPathTrait;

    public function create(PluginInformation $pluginInformation): void
    {
        $extensionPath = $this->getExtensionPath($pluginInformation->getExtensionKey());
        $targetFile = $extensionPath . 'ext_localconf.php';
        if (is_file($targetFile)) {
            try {
                $parser = (new ParserFactory())->createForHostVersion();
                $ast = $parser->parse(file_get_contents($targetFile));
                $traverser = new NodeTraverser();
                $traverser->addVisitor(new ExtLocalconfVisitor());

                $ast = $traverser->traverse($ast);
                // $stmts is an array of statement nodes
            } catch (Error $e) {
                return;
            }
        } else {
            $dumper = new Dumper();
            $dumper->indentation = '    ';
            file_put_contents(
                $targetFile,
                $dumper->format($this->getFileContent($pluginInformation)),
            );
        }
    }

    private function getFileContent(PluginInformation $pluginInformation): string
    {
        $factory = new BuilderFactory();
        $methodConfigurePlugin = $factory->staticCall(
            'ExtensionUtility',
            'configurePlugin',
            [
                $factory->val($pluginInformation->getExtensionName()),
                $factory->val($pluginInformation->getPluginName()),
                new Node\Expr\Array_([]),
            ]
        );

        $ast = [
            $factory->use('TYPO3\CMS\Extbase\Utility\ExtensionUtility')->getNode(),
            new Node\Stmt\Nop(),
            new Node\Stmt\Expression($methodConfigurePlugin),
        ];

        return (new Standard())->prettyPrintFile($ast);
    }
}
