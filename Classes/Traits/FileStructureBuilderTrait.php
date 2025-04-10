<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Visitor\FileVisitor;

trait FileStructureBuilderTrait
{
    private function buildFileStructure(string $filePath): FileStructure
    {
        // Early return, if file does not exist
        if (is_file($filePath) === false) {
            return new FileStructure();
        }

        $parser = (new ParserFactory())->createForHostVersion();
        $stmts = $parser->parse(file_get_contents($filePath));

        // This visitor loops through all nodes and collects them grouped in a FileStructure object
        $fileVisitor = new FileVisitor();

        $traverser = new NodeTraverser();
        $traverser->addVisitor($fileVisitor);
        $traverser->traverse($stmts);

        return $fileVisitor->getFileStructure();
    }
}
