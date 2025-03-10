<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder;

use StefanFroemken\ExtKickstarter\Model\Graph;
use StefanFroemken\ExtKickstarter\Model\Node\Main\ExtensionNode;

/**
 * Get file content for composer.json
 */
class ComposerJsonBuilder implements BuilderInterface
{
    public function build(Graph $graph, string $extPath): void
    {
        file_put_contents(
            $extPath . 'composer.json',
            $this->getFileContent($graph)
        );
    }

    private function getFileContent(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();

        return str_replace(
            [
                '{{NAME}}',
                '{{DESCRIPTION}}',
                '{{HOMEPAGE}}',
                '{{AUTOLOAD}}',
                '{{EXTENSION_KEY}}',
            ],
            [
                $extensionNode->getComposerName(),
                $extensionNode->getDescription(),
                $extensionNode->getProperties()['homepage'] ?? '',
                $this->getAutoload($extensionNode),
                $extensionNode->getExtensionKey(),
            ],
            $this->getTemplate()
        );
    }

    private function getAutoload(ExtensionNode $extensionNode): string
    {
        if (!$extensionNode->hasClasses()) {
            return '';
        }

        return $extensionNode->getNamespaceForAutoload();
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
{
	"name": "{{NAME}}",
	"type": "typo3-cms-extension",
	"description": "{{DESCRIPTION}}",
	"license": "GPL-2.0-or-later",
	"keywords": [
		"typo3"
	],
	"homepage": "{{HOMEPAGE}}",
	"require": {
		"typo3/cms-core": "^12.4.0"
	},
	"autoload": {
		"psr-4": {
			{{AUTOLOAD}}
		}
	},
	"replace": {
		"typo3-ter/{{EXTENSION_KEY}}": "self.version"
	},
	"extra": {
		"typo3/cms": {
			"extension-key": "{{EXTENSION_KEY}}"
		}
	}
}
EOT;
    }
}