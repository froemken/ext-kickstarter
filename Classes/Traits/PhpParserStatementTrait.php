<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

use PhpParser\Node;
use PhpParser\ParserFactory;

trait PhpParserStatementTrait
{
    private function getParserStatementsForFile(string $filePath): array
    {
        $parser = (new ParserFactory())->createForHostVersion();

        $statements = $parser->parse(file_get_contents($filePath));

        // parse() removes any kind of whitespace at end of file. Let us add an empty space again
        $statements[] = new Node\Stmt\Nop();

        return $statements;
    }
}