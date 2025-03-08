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
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\RepositoryNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for Extbase Repositories
 */
class RepositoryBuilder implements BuilderInterface
{
    public function build(Graph $graph, string $extPath): void
    {
        $repositoryNodes = $graph->getExtensionNode()->getExtbaseRepositoryNodes();
        if ($repositoryNodes->count() === 0) {
            return;
        }

        foreach ($repositoryNodes as $repositoryNode) {
            $repositoryPath = $extPath . '/Classes/Domain/Repository/';
            GeneralUtility::mkdir_deep($repositoryPath);

            file_put_contents(
                $repositoryPath . $repositoryNode->getRepositoryFilename(),
                $this->getFileContent($repositoryNode, $graph)
            );
        }
    }

    private function getFileContent(RepositoryNode $repositoryNode, Graph $graph): string
    {
        return str_replace(
            [
                '{{COMPOSER_NAME}}',
                '{{NAMESPACE}}',
                '{{MODEL}}',
                '{{REPOSITORY_NAME}}',
            ],
            [
                $graph->getExtensionNode()->getComposerName(),
                $repositoryNode->getNamespace(),
                $repositoryNode->getModelName(),
                $repositoryNode->getRepositoryName(),
            ],
            $this->getTemplate()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

declare(strict_types=1);

/*
 * This file is part of the package {{COMPOSER_NAME}}.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace {{NAMESPACE}};

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository to collect {{MODEL}} objects
 */
class {{REPOSITORY_NAME}} extends Repository {}
EOT;
    }
}
