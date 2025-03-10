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
 * Get file content for README.md
 */
class ReadmeBuilder implements BuilderInterface
{
    public function build(Graph $graph, string $extPath): void
    {
        file_put_contents(
            $extPath . 'README.md',
            $this->getFileContent($graph)
        );
    }

    private function getFileContent(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();

        return str_replace(
            [
                '{{TITLE}}',
                '{{DESCRIPTION}}',
            ],
            [
                $extensionNode->getTitle() ?? '',
                $extensionNode->getDescription(),
            ],
            $this->getTemplate()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
# %s

%s
EOT;
    }
}
