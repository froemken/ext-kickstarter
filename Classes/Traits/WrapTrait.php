<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

trait WrapTrait
{
    private function wrap(array $lines, array $beforeLines, array $afterLines, int $indents): array
    {
        $indent = '    ';

        $indentBefore = str_repeat($indent, $indents - 1);
        $indentLines = str_repeat($indent, $indents);
        $indentAfter = str_repeat($indent, $indents - 1);

        foreach ($lines as $key => $line) {
            $lines[$key] = $indentLines . $line;
        }

        $indentedLines = [];
        foreach ($beforeLines as $beforeLine) {
            $indentedLines[] = $indentBefore . $beforeLine;
        }

        array_push($indentedLines, ...$lines);

        foreach ($afterLines as $afterLine) {
            $indentedLines[] = $indentAfter . $afterLine;
        }

        return $indentedLines;
    }
}
