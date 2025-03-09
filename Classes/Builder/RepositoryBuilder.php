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
use StefanFroemken\ExtKickstarter\Traits\GetClassHeaderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for Extbase Repositories
 */
class RepositoryBuilder implements BuilderInterface
{
    use GetClassHeaderTrait;

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
                '{{CLASS_HEADER}}',
                '{{NAMESPACE}}',
                '{{REPOSITORY_NAME}}',
            ],
            [
                $this->getClassHeader($graph->getExtensionNode()),
                $repositoryNode->getNamespace(),
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

{{CLASS_HEADER}}

namespace {{NAMESPACE}};

use TYPO3\CMS\Extbase\Persistence\Repository;

class {{REPOSITORY_NAME}} extends Repository {}
EOT;
    }
}
