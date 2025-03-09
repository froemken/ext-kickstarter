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
 * Get file content for .gitattributes
 */
class GitAttributesBuilder implements BuilderInterface
{
    public function build(Graph $graph, string $extPath): void
    {
        file_put_contents(
            $extPath . '.gitattributes',
            $this->getTemplate()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
/.editorconfig export-ignore
/.gitattributes export-ignore
/.gitignore export-ignore
/.phpstorm.meta.php export-ignore
EOT;
    }
}
