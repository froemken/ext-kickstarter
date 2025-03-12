<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Plugin;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\Printer\PrettyTypo3Printer;
use StefanFroemken\ExtKickstarter\Traits\ExtensionPathTrait;
use StefanFroemken\ExtKickstarter\Traits\PhpParserStatementTrait;

class ExtbasePluginCreator
{
    use ExtensionPathTrait;
    use PhpParserStatementTrait;

    private PrettyTypo3Printer $prettyTypo3Printer;

    public function __construct(
        PrettyTypo3Printer $prettyTypo3Printer,
    ) {
        $this->prettyTypo3Printer = $prettyTypo3Printer;
    }

    public function create(PluginInformation $pluginInformation): void
    {
        $builderFactory = new BuilderFactory();
        $extensionPath = $this->getExtensionPath($pluginInformation->getExtensionKey());
        $targetFile = $extensionPath . 'ext_localconf.php';

        if (is_file($targetFile)) {
            file_put_contents(
                $targetFile,
                $this->prettyTypo3Printer->prettyPrint(
                    $this->getAstForConfigurePlugin($pluginInformation, $builderFactory),
                ),
                FILE_APPEND,
            );
        } else {
            $ast = [
                $builderFactory->use('TYPO3\CMS\Extbase\Utility\ExtensionUtility')->getNode(),
            ];
            array_push($ast, ...$this->getAstForConfigurePlugin($pluginInformation, $builderFactory));
            file_put_contents(
                $targetFile,
                $this->prettyTypo3Printer->prettyPrintFile($ast),
            );
        }
    }

    private function getAstForConfigurePlugin(
        PluginInformation $pluginInformation,
        BuilderFactory $builderFactory
    ): array {
        if ($pluginInformation->getPluginType() === 'plugin') {
            $pluginTypeNode = new Node\Expr\ClassConstFetch(
                new Node\Name('TYPO3\CMS\Extbase\Utility\ExtensionUtility'),
                'PLUGIN_TYPE_PLUGIN'
            );
        } else {
            $pluginTypeNode = new Node\Expr\ClassConstFetch(
                new Node\Name('TYPO3\CMS\Extbase\Utility\ExtensionUtility'),
                'PLUGIN_TYPE_CONTENT_ELEMENT'
            );
        }

        $configurePluginNode = $builderFactory->staticCall(
            'ExtensionUtility',
            'configurePlugin',
            [
                $pluginInformation->getExtensionName(),
                $pluginInformation->getPluginName(),
                new Node\Expr\Array_([]),
                new Node\Expr\Array_([]),
                $pluginTypeNode,
            ]
        );

        return [
            new Node\Stmt\Nop(),
            new Node\Stmt\Expression($configurePluginNode),
            new Node\Stmt\Nop(),
        ];
    }
}
