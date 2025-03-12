<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Extension;

use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Traits\ExtensionPathTrait;

class EditorConfigCreator implements ExtensionCreatorInterface
{
    use ExtensionPathTrait;

    public function create(ExtensionInformation $extensionInformation): void
    {
        $extensionPath = $this->getExtensionPath($extensionInformation->getExtensionKey());

        file_put_contents(
            $extensionPath . '.editorconfig',
            $this->getTemplate(),
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
# EditorConfig is awesome: http://EditorConfig.org

# top-most EditorConfig file
root = true

# Unix-style newlines with a newline ending every file
[*]
charset = utf-8
end_of_line = lf
indent_style = space
indent_size = 4
insert_final_newline = true
trim_trailing_whitespace = true

# TS/JS-Files
[*.{ts,js,mjs}]
indent_size = 2

# JSON-Files
[*.json]
indent_style = tab

# ReST-Files
[*.{rst,rst.txt}]
indent_size = 4
max_line_length = 80

# Markdown-Files
[*.md]
max_line_length = 80

# YAML-Files
[*.{yaml,yml}]
indent_size = 2

# NEON-Files
[*.neon]
indent_size = 2
indent_style = tab

# stylelint
[.stylelintrc]
indent_size = 2

# package.json
[package.json]
indent_size = 2

# TypoScript
[*.{typoscript,tsconfig}]
indent_size = 2

# XLF-Files
[*.xlf]
indent_style = tab

# SQL-Files
[*.sql]
indent_style = tab
indent_size = 2

# .htaccess
[{_.htaccess,.htaccess}]
indent_style = tab
EOT;
    }
}
