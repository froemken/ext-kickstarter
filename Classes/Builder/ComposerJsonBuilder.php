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
                '{{EXTENSION_KEY}}',
            ],
            [
                $extensionNode->getProperties()['composerName'] ?? '',
                $extensionNode->getProperties()['description'] ?? '',
                $extensionNode->getProperties()['homepage'] ?? '',
                $extensionNode->getProperties()['extensionKey'] ?? '',
            ],
            $this->getTemplate()
        );
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
	"homepage": "{{HOMEPAGE}}}}",
	"require": {
		"typo3/cms-core": "^12.4.0"
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